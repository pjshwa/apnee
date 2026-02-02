<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pubble - Shared Level</title>
    <link rel="stylesheet" href="puzzle_game.css">
    <style>
        .level-info {
            background: rgba(99, 102, 241, 0.1);
            border: 2px solid rgba(99, 102, 241, 0.3);
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 16px;
            text-align: center;
        }
        
        .level-info h2 {
            margin: 0 0 8px 0;
            color: #6366f1;
        }
        
        .level-info p {
            margin: 4px 0;
            color: #94a3b8;
        }
        
        .level-stats {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 12px;
            font-size: 14px;
        }
        
        .level-stats span {
            color: #94a3b8;
        }
        
        .back-btn {
            background: rgba(99, 102, 241, 0.2);
            color: #fff;
            border: 2px solid rgba(99, 102, 241, 0.5);
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
            margin-top: 8px;
        }
        
        .back-btn:hover {
            background: rgba(99, 102, 241, 0.3);
            border-color: rgba(99, 102, 241, 0.7);
        }
        
        .error-message {
            background: rgba(239, 68, 68, 0.1);
            border: 2px solid rgba(239, 68, 68, 0.3);
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            color: #ef4444;
        }
        
        .loading {
            text-align: center;
            padding: 40px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="game-container">
        <div class="game-header">
            <h1>Pubble</h1>
            <p class="game-subtitle">Shared Level</p>
            
            <div id="level-info-container"></div>
            
            <div class="game-info">
                <span>Level: <span id="current-level">-</span></span>
                <button id="reset-btn">Restart</button>
                <button id="next-level-btn" style="display:none;">Next Level</button>
                <a href="index.html" class="back-btn">← Back to Main</a>
                <a href="gallery.html" class="back-btn">Level Gallery</a>
            </div>
        </div>
        
        <div class="game-board" id="game-board">
            <!-- 15x15 그리드가 여기에 생성됩니다 -->
        </div>
        
        <div class="controls">
            <h3>Controls:</h3>
            <p><strong>WASD</strong> or <strong>Arrow Keys</strong> to move</p>
            <p><strong>Spacebar</strong> to jump (1 cell only)</p>
            <p>Get close to <strong>🔴 Switch</strong> to activate and reveal connected <strong>🟣 Toggle Blocks</strong></p>
            <p><strong>Goal</strong>: Reach the 🏁!</p>
        </div>
    </div>
    
    <div id="victory-message" class="victory-message" style="display:none;">
        <div class="victory-content">
            <h2>🎉 Clear!</h2>
            <p>Congratulations!</p>
            <button id="continue-btn">Go to Gallery</button>
        </div>
    </div>

    <script src="puzzle_game.js"></script>
    <script>
        // URL에서 레벨 ID 가져오기
        const urlParams = new URLSearchParams(window.location.search);
        const levelId = urlParams.get('id');
        
        if (!levelId) {
            document.getElementById('level-info-container').innerHTML = `
                <div class="error-message">
                    <h2>Error</h2>
                    <p>Level ID not specified.</p>
                </div>
            `;
        } else {
            // 로딩 표시
            document.getElementById('level-info-container').innerHTML = `
                <div class="loading">Loading level...</div>
            `;
            
            // 레벨 데이터 가져오기
            fetch(`api_get_level.php?id=${levelId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const level = data.level;
                        
                        // 레벨 정보 표시
                        document.getElementById('level-info-container').innerHTML = `
                            <div class="level-info">
                                <h2>${escapeHtml(level.name)}</h2>
                                ${level.description ? `<p>${escapeHtml(level.description)}</p>` : ''}
                                <div class="level-stats">
                                    <span>👁️ ${level.plays} plays</span>
                                    <span>❤️ ${level.likes} likes</span>
                                    <span>📅 ${new Date(level.created_at).toLocaleDateString()}</span>
                                </div>
                            </div>
                        `;
                        
                        // 게임 시작
                        window.sharedLevelData = level.data;
                        
                        // 리셋 버튼 오버라이드 (공유 레벨 다시 로드)
                        window.addEventListener('gameReady', () => {
                            const resetBtn = document.getElementById('reset-btn');
                            if (resetBtn && window.game) {
                                // 기존 이벤트 제거 후 새 이벤트 추가
                                const newResetBtn = resetBtn.cloneNode(true);
                                resetBtn.parentNode.replaceChild(newResetBtn, resetBtn);
                                
                                newResetBtn.addEventListener('click', () => {
                                    window.game.loadCustomLevel(window.sharedLevelData);
                                });
                            }
                            
                            // 계속하기 버튼도 오버라이드 (갤러리로 이동)
                            const continueBtn = document.getElementById('continue-btn');
                            if (continueBtn) {
                                const newContinueBtn = continueBtn.cloneNode(true);
                                continueBtn.parentNode.replaceChild(newContinueBtn, continueBtn);
                                
                                newContinueBtn.addEventListener('click', () => {
                                    window.location.href = 'gallery.html';
                                });
                            }
                        });
                        
                        // 게임 초기화 (puzzle_game.js의 DOMContentLoaded 이벤트가 실행됨)
                        if (window.game) {
                            window.game.loadCustomLevel(level.data);
                        }
                    } else {
                        document.getElementById('level-info-container').innerHTML = `
                            <div class="error-message">
                                <h2>Error</h2>
                                <p>${escapeHtml(data.error || 'Failed to load level.')}</p>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('level-info-container').innerHTML = `
                        <div class="error-message">
                            <h2>Error</h2>
                            <p>An error occurred while loading the level.</p>
                        </div>
                    `;
                });
        }
        
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>
</body>
</html>
