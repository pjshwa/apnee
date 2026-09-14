"""Exercise the real SSH deployment scripts with local Git/AWS substitutes.

Run with: python3 -m unittest discover -s tests -p 'test_deploy.py'
No network, production credentials, or live deployment directories are used.
"""
import os
from pathlib import Path
import re
import shutil
import subprocess
import tempfile
import textwrap
import unittest


ROOT = Path(__file__).resolve().parents[1]


class DeploymentTest(unittest.TestCase):
    def run_deployment(self, target, failure):
        with tempfile.TemporaryDirectory(prefix='apnee-deploy-test-') as directory:
            sandbox = Path(directory)
            deployment = sandbox / 'deployment'
            releases = deployment / 'releases'
            active = releases / '20000101000000'
            active.mkdir(parents=True)
            (active / 'index.php').write_text('<?php echo "old";')
            (deployment / 'current').symlink_to(active)
            (deployment / 'repo').mkdir()
            for number in range(1, 7):
                (releases / ('2000010100000' + str(number))).mkdir()

            fixture = sandbox / 'fixture'
            fixture.mkdir()
            for filename in ['index.php', 'db.php']:
                (fixture / filename).write_text('<?php // valid release')
            commands = sandbox / 'commands'
            commands.mkdir()
            (commands / 'git').write_text(textwrap.dedent('''\
                #!/bin/bash
                case "$1" in
                  fetch) [ "$DEPLOY_TEST_FAILURE" != fetch ] ;;
                  archive)
                    case "$DEPLOY_TEST_FAILURE" in
                      archive) exit 1 ;;
                      empty-archive) tar -cf - --files-from /dev/null ;;
                      partial-archive)
                        tar -cf - -C "$DEPLOY_TEST_FIXTURE" .
                        exit 1 ;;
                      *) tar -cf - -C "$DEPLOY_TEST_FIXTURE" . ;;
                    esac ;;
                  *) exit 1 ;;
                esac
                '''))
            (commands / 'aws').write_text('#!/bin/bash\n[ "$DEPLOY_TEST_FAILURE" != assets ]\n')
            for executable in commands.iterdir():
                executable.chmod(0o755)

            workflow = (ROOT / '.github/workflows' / ('deploy-' + target + '.yml')).read_text()
            script = workflow.split('        script: |\n', 1)[1].split('\n    - name:', 1)[0]
            script = textwrap.dedent(script)
            script = script.replace('/home/bitnami/htdocs-' + ('prod' if target == 'prod' else 'test'), str(deployment))
            script = script.replace('/opt/bitnami/php/bin/php', shutil.which('php'))
            script = re.sub(r'\$\{\{ secrets\.[A-Z_]+ \}\}', 'test_dummy', script)
            if failure == 'credentials':
                script = script.replace("'pass' => 'test_dummy'", "'pass' => 'invalid'quote'")
            self.assertNotIn('/home/bitnami', script)
            self.assertNotIn('${{', script)
            environment = dict(os.environ, PATH=str(commands) + os.pathsep + os.environ['PATH'],
                               DEPLOY_TEST_FAILURE=failure, DEPLOY_TEST_FIXTURE=str(fixture))
            subprocess.run(['bash', '-n'], input=script, text=True, check=True, capture_output=True)
            result = subprocess.run(['bash'], input=script, text=True, env=environment,
                                    capture_output=True, timeout=15)
            current = deployment / 'current'
            self.assertTrue(current.is_symlink())
            if failure:
                self.assertNotEqual(result.returncode, 0, result.stdout + result.stderr)
                self.assertEqual(current.resolve(), active)
                self.assertTrue((active / 'index.php').is_file())
                # Failed deployments must not prune any previous release.
                self.assertGreaterEqual(len(list(releases.iterdir())), 7)
            else:
                self.assertEqual(result.returncode, 0, result.stdout + result.stderr)
                self.assertNotEqual(current.resolve(), active)
                for filename in ['index.php', 'db.php', 'credentials.php']:
                    self.assertTrue((current / filename).is_file())
                if target == 'stag':
                    self.assertEqual(len(list(releases.iterdir())), 5)

    def test_failed_deployments_preserve_current(self):
        for target in ['prod', 'stag']:
            for failure in ['fetch', 'archive', 'partial-archive', 'empty-archive', 'credentials', 'assets']:
                with self.subTest(target=target, failure=failure):
                    self.run_deployment(target, failure)

    def test_complete_deployments_publish_release(self):
        for target in ['prod', 'stag']:
            with self.subTest(target=target):
                self.run_deployment(target, '')


if __name__ == '__main__':
    unittest.main()
