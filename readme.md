### 주소
- https://pjshwa.me

### 지하철 도착정보 인증키
- 서울 열린데이터광장에서 발급한 **실시간 지하철 인증키**를 사용합니다.
- 로컬에서는 Git에서 제외된 `credentials.php`의 `$credentials` 배열에 `'seoul_subway_api_key' => '발급받은 키'`를 추가합니다.
- 배포 시에는 GitHub Actions Secret `SEOUL_SUBWAY_API_KEY`를 설정하면 배포 워크플로우가 같은 항목을 생성합니다.
