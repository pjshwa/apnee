class LevelEditor {
    constructor(game) {
        this.game = game;
        this.isActive = false;
        this.isTestPlaying = false;
        this.currentTool = 'wall';
        this.selectedSwitch = null;
        this.customLevel = null;
        this.startPosition = null;
        this.connectionLines = [];
        
        // 드래그 관련
        this.isDragging = false;
        this.draggedObject = null;
        this.draggedFromX = -1;
        this.draggedFromY = -1;
        this.dragGhost = null;
        
        // 바인딩된 이벤트 핸들러 저장 (removeEventListener를 위해)
        this.boundHandleCellClick = this.handleCellClick.bind(this);
        this.boundHandleDragStart = this.handleDragStart.bind(this);
        this.boundHandleDragMove = this.handleDragMove.bind(this);
        this.boundHandleDragEnd = this.handleDragEnd.bind(this);
        
        this.ready = this.init();
    }
    
    init() {
        this.createConnectionCanvas();
        this.createDragGhost();
        this.bindEvents();
    }
    
    createConnectionCanvas() {
        // SVG 캔버스를 만들어서 연결선을 그림
        this.svgCanvas = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
        this.svgCanvas.style.cssText = `
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 50;
        `;
        this.svgCanvas.id = 'connection-canvas';
    }
    
    createDragGhost() {
        // 드래그 시 보여줄 고스트 요소
        this.dragGhost = document.createElement('div');
        this.dragGhost.className = 'drag-ghost';
        this.dragGhost.style.cssText = `
            position: fixed;
            pointer-events: none;
            z-index: 1000;
            opacity: 0.8;
            transform: translate(-50%, -50%);
            display: none;
            font-size: 24px;
            background: rgba(99, 102, 241, 0.3);
            border: 2px dashed #6366f1;
            border-radius: 4px;
            padding: 4px 8px;
        `;
        document.body.appendChild(this.dragGhost);
    }
    
    bindEvents() {
        // 에디터 토글
        const editorBtn = document.getElementById('editor-btn');
        if (editorBtn) {
            editorBtn.addEventListener('click', () => {
                this.toggle();
            });
        }
        
        // 도구 선택
        document.querySelectorAll('.tool-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                if (btn.dataset.tool) {
                    this.selectTool(btn.dataset.tool);
                }
            });
        });
        
        // 레벨 관리 버튼들
        const testLevelBtn = document.getElementById('test-level-btn');
        if (testLevelBtn) {
            testLevelBtn.addEventListener('click', () => {
                this.testLevel();
            });
        }
        
        const clearLevelBtn = document.getElementById('clear-level-btn');
        if (clearLevelBtn) {
            clearLevelBtn.addEventListener('click', () => {
                this.clearLevel();
            });
        }
        
        const exportLevelBtn = document.getElementById('export-level-btn');
        if (exportLevelBtn) {
            exportLevelBtn.addEventListener('click', () => {
                this.exportLevel();
            });
        }
        
        const importLevelBtn = document.getElementById('import-level-btn');
        if (importLevelBtn) {
            importLevelBtn.addEventListener('click', () => {
                this.importLevel();
            });
        }
        
        const shareLevelBtn = document.getElementById('share-level-btn');
        if (shareLevelBtn) {
            shareLevelBtn.addEventListener('click', () => {
                this.shareLevel();
            });
        }
        
        // Download JSON button
        const downloadJsonBtn = document.getElementById('download-json-btn');
        if (downloadJsonBtn) {
            downloadJsonBtn.addEventListener('click', () => {
                const json = downloadJsonBtn.dataset.json;
                if (json) {
                    const blob = new Blob([json], { type: 'application/json' });
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    
                    // Generate filename with timestamp
                    const timestamp = new Date().toISOString().replace(/[:.]/g, '-').slice(0, -5);
                    a.download = `level_${timestamp}.json`;
                    
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    URL.revokeObjectURL(url);
                }
            });
        }
    }
    
    toggle() {
        this.isActive = !this.isActive;
        const panel = document.getElementById('editor-panel');
        panel.style.display = this.isActive ? 'block' : 'none';
        
        if (this.isActive) {
            this.enterEditorMode();
            // URL 업데이트
            this.game.updateURL('editor');
        } else {
            this.exitEditorMode();
            // URL 업데이트 (현재 레벨로)
            this.game.updateURL('level/' + this.game.currentLevel);
        }
    }
    
    enterEditorMode() {
        // 게임 정지
        this.game.gameWon = true;
        
        // 현재 보드에서 시작점 찾기 및 표시
        this.startPosition = null;
        for (let y = 0; y < this.game.gridSize; y++) {
            for (let x = 0; x < this.game.gridSize; x++) {
                if (this.game.board[y][x].type === 'start') {
                    this.startPosition = { x, y };
                    // 시작점 시각적 표시
                    this.game.board[y][x].element.classList.add('start-point');
                    this.game.board[y][x].element.innerHTML = '🧙‍♂️';
                    break;
                }
            }
            if (this.startPosition) break;
        }
        
        // SVG 캔버스 추가
        const gameBoard = document.getElementById('game-board');
        gameBoard.appendChild(this.svgCanvas);
        
        // 클릭 및 드래그 이벤트 추가
        document.querySelectorAll('.cell').forEach(cell => {
            cell.addEventListener('click', this.boundHandleCellClick);
            cell.addEventListener('mousedown', this.boundHandleDragStart);
            cell.style.cursor = 'pointer';
        });
        
        // 전역 드래그 이벤트
        document.addEventListener('mousemove', this.boundHandleDragMove);
        document.addEventListener('mouseup', this.boundHandleDragEnd);
        
        this.updateConnectionLines();
        this.updateSwitchInfo();
    }
    
    exitEditorMode() {
        // SVG 캔버스 제거
        this.svgCanvas.remove();
        
        // 클릭 및 드래그 이벤트 제거
        document.querySelectorAll('.cell').forEach(cell => {
            cell.removeEventListener('click', this.boundHandleCellClick);
            cell.removeEventListener('mousedown', this.boundHandleDragStart);
            cell.style.cursor = 'default';
            cell.classList.remove('selected-switch', 'linked-block');
        });
        
        // 시작점 시각적 표시 제거 (내부 타입은 유지)
        if (this.startPosition) {
            const startCell = this.game.board[this.startPosition.y][this.startPosition.x];
            startCell.element.classList.remove('start-point');
            startCell.element.innerHTML = '';
        }
        
        // 전역 드래그 이벤트 제거
        document.removeEventListener('mousemove', this.boundHandleDragMove);
        document.removeEventListener('mouseup', this.boundHandleDragEnd);
        
        this.selectedSwitch = null;
        this.isDragging = false;
        this.draggedObject = null;
        this.dragGhost.style.display = 'none';
        this.updateSwitchInfo();
    }
    
    selectTool(tool) {
        this.currentTool = tool;
        document.querySelectorAll('.tool-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        document.querySelector(`[data-tool="${tool}"]`).classList.add('active');
        
        // 도구 변경 시 스위치 선택 해제
        if (tool !== 'switch' && tool !== 'toggle-block') {
            this.deselectSwitch();
        }
    }
    
    // 드래그 시작
    handleDragStart(event) {
        if (!this.isActive) return;
        
        const cell = event.target.closest('.cell');
        if (!cell) return;
        
        const x = parseInt(cell.dataset.x);
        const y = parseInt(cell.dataset.y);
        const boardCell = this.game.board[y][x];
        
        // 이동 가능한 오브젝트만 드래그 가능 (빈 칸 제외)
        if (boardCell.type !== 'empty') {
            this.isDragging = true;
            this.draggedFromX = x;
            this.draggedFromY = y;
            this.draggedObject = {
                type: boardCell.type,
                switchData: boardCell.switchData ? JSON.parse(JSON.stringify(boardCell.switchData)) : null
            };
            
            // 고스트 표시
            this.updateDragGhost(boardCell.type);
            this.dragGhost.style.display = 'block';
            this.dragGhost.style.left = event.clientX + 'px';
            this.dragGhost.style.top = event.clientY + 'px';
            
            // 원래 셀에 드래그 중 표시
            cell.classList.add('dragging');
            
            event.preventDefault();
        }
    }
    
    // 드래그 중
    handleDragMove(event) {
        if (!this.isDragging) return;
        
        this.dragGhost.style.left = event.clientX + 'px';
        this.dragGhost.style.top = event.clientY + 'px';
    }
    
    // 드래그 종료
    handleDragEnd(event) {
        if (!this.isDragging) return;
        
        // 드래그 중 표시 제거
        document.querySelectorAll('.cell.dragging').forEach(c => {
            c.classList.remove('dragging');
        });
        
        // 드롭 위치 찾기
        const gameBoard = document.getElementById('game-board');
        const boardRect = gameBoard.getBoundingClientRect();
        const cellSize = boardRect.width / this.game.gridSize;
        
        const dropX = Math.floor((event.clientX - boardRect.left) / cellSize);
        const dropY = Math.floor((event.clientY - boardRect.top) / cellSize);
        
        // 유효한 위치인지 확인
        if (dropX >= 0 && dropX < this.game.gridSize && 
            dropY >= 0 && dropY < this.game.gridSize &&
            (dropX !== this.draggedFromX || dropY !== this.draggedFromY)) {
            
            // 이동 수행
            this.moveObject(this.draggedFromX, this.draggedFromY, dropX, dropY);
        }
        
        // 드래그 상태 초기화
        this.isDragging = false;
        this.draggedObject = null;
        this.draggedFromX = -1;
        this.draggedFromY = -1;
        this.dragGhost.style.display = 'none';
    }
    
    // 오브젝트 이동
    moveObject(fromX, fromY, toX, toY) {
        const fromCell = this.game.board[fromY][fromX];
        const toCell = this.game.board[toY][toX];
        
        // 원본 정보 저장
        const objectType = fromCell.type;
        const objectSwitchData = fromCell.switchData;
        
        // 목표 위치에 이미 오브젝트가 있으면 스왑 또는 무시
        if (toCell.type !== 'empty') {
            // 덮어쓰지 않음 - 빈 칸으로만 이동 가능
            return;
        }
        
        // 원래 위치 비우기
        fromCell.element.className = 'cell';
        fromCell.element.innerHTML = '';
        fromCell.type = 'empty';
        fromCell.switchData = null;
        fromCell.wallActive = false;
        
        // 새 위치에 배치
        toCell.type = objectType;
        toCell.switchData = objectSwitchData;
        
        switch(objectType) {
            case 'wall':
                toCell.element.classList.add('wall');
                break;
            case 'switch':
                toCell.element.classList.add('switch');
                // 스위치 위치 업데이트
                if (toCell.switchData) {
                    toCell.switchData.x = toX;
                    toCell.switchData.y = toY;
                    // 선택된 스위치 업데이트
                    if (this.selectedSwitch && 
                        this.selectedSwitch.x === fromX && 
                        this.selectedSwitch.y === fromY) {
                        this.selectedSwitch = toCell.switchData;
                    }
                }
                break;
            case 'toggle-block':
                toCell.element.classList.add('toggle-block');
                // 연결된 스위치들의 블록 위치 업데이트
                this.updateBlockPositionInSwitches(fromX, fromY, toX, toY);
                break;
            case 'goal':
                toCell.element.classList.add('goal');
                toCell.element.innerHTML = '🏁';
                break;
            case 'start':
                toCell.element.classList.add('start-point');
                toCell.element.innerHTML = '🧙‍♂️';
                this.startPosition = { x: toX, y: toY };
                break;
        }
        
        this.updateConnectionLines();
    }
    
    // 스위치에 연결된 블록 위치 업데이트
    updateBlockPositionInSwitches(fromX, fromY, toX, toY) {
        for (let sy = 0; sy < this.game.gridSize; sy++) {
            for (let sx = 0; sx < this.game.gridSize; sx++) {
                const cell = this.game.board[sy][sx];
                if (cell.type === 'switch' && cell.switchData) {
                    cell.switchData.blocks.forEach(block => {
                        if (block.x === fromX && block.y === fromY) {
                            block.x = toX;
                            block.y = toY;
                        }
                    });
                }
            }
        }
    }
    
    // 드래그 고스트 업데이트
    updateDragGhost(type) {
        let emoji = '';
        switch(type) {
            case 'wall': emoji = '🧱'; break;
            case 'switch': emoji = '🔴'; break;
            case 'toggle-block': emoji = '🟣'; break;
            case 'goal': emoji = '🏁'; break;
            case 'start': emoji = '🧙‍♂️'; break;
            default: emoji = '❓';
        }
        this.dragGhost.textContent = emoji;
    }
    
    handleCellClick(event) {
        if (!this.isActive) return;
        // 드래그 중이면 클릭 무시
        if (this.isDragging) return;
        
        const cell = event.target.closest('.cell');
        if (!cell) return;
        
        const x = parseInt(cell.dataset.x);
        const y = parseInt(cell.dataset.y);
        const boardCell = this.game.board[y][x];
        
        // 스위치가 선택된 상태에서 토글블록 클릭 시 연결/해제
        if (this.selectedSwitch && boardCell.type === 'toggle-block') {
            this.toggleBlockConnection(x, y);
            return;
        }
        
        // 스위치 클릭 시 선택
        if (boardCell.type === 'switch' && boardCell.switchData) {
            if (this.currentTool === 'empty') {
                this.placeTile(x, y);
            } else {
                this.selectSwitch(boardCell.switchData, cell);
            }
            return;
        }
        
        // 일반 타일 배치
        this.placeTile(x, y);
    }
    
    selectSwitch(switchData, cellElement) {
        // 이전 선택 해제
        document.querySelectorAll('.cell.selected-switch').forEach(c => {
            c.classList.remove('selected-switch');
        });
        document.querySelectorAll('.cell.linked-block').forEach(c => {
            c.classList.remove('linked-block');
        });
        
        this.selectedSwitch = switchData;
        cellElement.classList.add('selected-switch');
        
        // 연결된 블록 표시 (경계 체크 포함)
        switchData.blocks.forEach(block => {
            if (block.x >= 0 && block.x < this.game.gridSize && 
                block.y >= 0 && block.y < this.game.gridSize) {
                const blockCell = this.game.board[block.y][block.x];
                blockCell.element.classList.add('linked-block');
            }
        });
        
        this.updateSwitchInfo();
        this.updateConnectionLines();
    }
    
    deselectSwitch() {
        document.querySelectorAll('.cell.selected-switch').forEach(c => {
            c.classList.remove('selected-switch');
        });
        document.querySelectorAll('.cell.linked-block').forEach(c => {
            c.classList.remove('linked-block');
        });
        
        this.selectedSwitch = null;
        this.updateSwitchInfo();
        this.updateConnectionLines();
    }
    
    toggleBlockConnection(x, y) {
        const blockPos = { x, y };
        const existingIndex = this.selectedSwitch.blocks.findIndex(b => b.x === x && b.y === y);
        const blockCell = this.game.board[y][x];
        
        if (existingIndex >= 0) {
            // 연결 해제
            this.selectedSwitch.blocks.splice(existingIndex, 1);
            blockCell.element.classList.remove('linked-block');
        } else {
            // 연결 추가
            this.selectedSwitch.blocks.push(blockPos);
            blockCell.element.classList.add('linked-block');
        }
        
        this.updateSwitchInfo();
        this.updateConnectionLines();
    }
    
    updateConnectionLines() {
        // SVG 초기화
        this.svgCanvas.innerHTML = '';
        
        if (!this.selectedSwitch) return;
        
        const gameBoard = document.getElementById('game-board');
        const boardRect = gameBoard.getBoundingClientRect();
        const cellSize = boardRect.width / this.game.gridSize;
        
        const switchX = this.selectedSwitch.x * cellSize + cellSize / 2;
        const switchY = this.selectedSwitch.y * cellSize + cellSize / 2;
        
        this.selectedSwitch.blocks.forEach(block => {
            const blockX = block.x * cellSize + cellSize / 2;
            const blockY = block.y * cellSize + cellSize / 2;
            
            const line = document.createElementNS('http://www.w3.org/2000/svg', 'line');
            line.setAttribute('x1', switchX);
            line.setAttribute('y1', switchY);
            line.setAttribute('x2', blockX);
            line.setAttribute('y2', blockY);
            line.setAttribute('stroke', '#8b5cf6');
            line.setAttribute('stroke-width', '3');
            line.setAttribute('stroke-dasharray', '8,4');
            line.setAttribute('opacity', '0.8');
            
            this.svgCanvas.appendChild(line);
        });
    }
    
    updateSwitchInfo() {
        const info = document.getElementById('switch-info');
        
        if (this.selectedSwitch) {
            const count = this.selectedSwitch.blocks.length;
            info.innerHTML = `
                <div class="switch-selected">
                    <span>🔴 스위치 (${this.selectedSwitch.x}, ${this.selectedSwitch.y}) 선택됨</span>
                    <span>연결된 블록: <strong>${count}개</strong></span>
                </div>
            `;
        } else {
            info.innerHTML = '<span class="no-switch">스위치를 클릭하여 선택하세요</span>';
        }
    }
    
    placeTile(x, y) {
        const cell = this.game.board[y][x];
        const oldType = cell.type;
        
        // 스위치를 지우는 경우 선택 해제
        if (oldType === 'switch' && cell.switchData) {
            if (this.selectedSwitch === cell.switchData) {
                this.deselectSwitch();
            }
        }
        
        // 시작점을 지우는 경우
        if (oldType === 'start') {
            this.startPosition = null;
        }
        
        // 토글블록을 지우는 경우 연결된 스위치에서 제거
        if (oldType === 'toggle-block') {
            this.removeBlockFromAllSwitches(x, y);
        }
        
        // 기존 타입 제거
        cell.element.className = 'cell';
        cell.element.innerHTML = '';
        cell.type = 'empty';
        cell.switchData = null;
        cell.wallActive = false;
        
        switch(this.currentTool) {
            case 'wall':
                cell.type = 'wall';
                cell.element.classList.add('wall');
                break;
            case 'switch':
                cell.type = 'switch';
                cell.element.classList.add('switch');
                cell.switchData = { x, y, blocks: [] };
                this.selectSwitch(cell.switchData, cell.element);
                break;
            case 'toggle-block':
                cell.type = 'toggle-block';
                cell.element.classList.add('toggle-block');
                // 선택된 스위치가 있으면 자동 연결
                if (this.selectedSwitch) {
                    this.selectedSwitch.blocks.push({ x, y });
                    cell.element.classList.add('linked-block');
                    this.updateSwitchInfo();
                    this.updateConnectionLines();
                }
                break;
            case 'goal':
                cell.type = 'goal';
                cell.element.classList.add('goal');
                cell.element.innerHTML = '🏁';
                break;
            case 'start':
                // 기존 시작점 표시 제거
                for (let sy = 0; sy < this.game.gridSize; sy++) {
                    for (let sx = 0; sx < this.game.gridSize; sx++) {
                        const startCell = this.game.board[sy][sx];
                        if (startCell.type === 'start') {
                            startCell.type = 'empty';
                            startCell.element.classList.remove('start-point');
                            startCell.element.innerHTML = '';
                        }
                    }
                }
                cell.type = 'start';
                cell.element.classList.add('start-point');
                cell.element.innerHTML = '🧙‍♂️';
                this.startPosition = { x, y };
                break;
            case 'empty':
                // 이미 위에서 처리됨
                break;
        }
    }
    
    removeBlockFromAllSwitches(x, y) {
        // 모든 스위치에서 해당 블록 제거
        for (let sy = 0; sy < this.game.gridSize; sy++) {
            for (let sx = 0; sx < this.game.gridSize; sx++) {
                const cell = this.game.board[sy][sx];
                if (cell.type === 'switch' && cell.switchData) {
                    const idx = cell.switchData.blocks.findIndex(b => b.x === x && b.y === y);
                    if (idx >= 0) {
                        cell.switchData.blocks.splice(idx, 1);
                    }
                }
            }
        }
        this.updateSwitchInfo();
        this.updateConnectionLines();
    }
    
    clearLevel() {
        if (!confirm('전체 레벨을 지우시겠습니까?')) return;
        
        for (let y = 0; y < this.game.gridSize; y++) {
            for (let x = 0; x < this.game.gridSize; x++) {
                const cell = this.game.board[y][x];
                cell.element.className = 'cell';
                cell.element.innerHTML = '';
                cell.type = 'empty';
                cell.switchData = null;
            }
        }
        
        this.deselectSwitch();
        this.startPosition = null;
    }
    
    testLevel() {
        // 시작점 체크
        let startCount = 0;
        let foundStart = null;
        for (let y = 0; y < this.game.gridSize; y++) {
            for (let x = 0; x < this.game.gridSize; x++) {
                if (this.game.board[y][x].type === 'start') {
                    startCount++;
                    foundStart = { x, y };
                }
            }
        }
        
        if (startCount === 0) {
            alert('⚠️ 시작점이 없습니다! 🧙‍♂️ 시작점을 배치해주세요.');
            return;
        }
        
        if (startCount > 1) {
            alert('⚠️ 시작점이 ' + startCount + '개 있습니다! 시작점은 1개만 있어야 합니다.');
            return;
        }
        
        // 시작 위치 업데이트
        this.startPosition = foundStart;
        
        this.customLevel = this.exportLevelData();
        this.isTestPlaying = true;
        this.toggle(); // 에디터 종료
        this.game.loadCustomLevel(this.customLevel);
    }
    
    // 테스트 플레이 종료 후 에디터로 돌아가기
    returnToEditor() {
        this.isTestPlaying = false;
        document.getElementById('victory-message').style.display = 'none';
        this.toggle(); // 에디터 활성화
        // 커스텀 레벨 다시 로드
        if (this.customLevel) {
            this.game.loadCustomLevel(this.customLevel);
            this.game.gameWon = true; // 에디터 모드에서는 게임 정지
        }
    }
    
    exportLevel() {
        const levelData = this.exportLevelData();
        // Compact JSON with minimal newlines for easier human editing
        const json = JSON.stringify(levelData);
        
        // Show in textarea and copy to clipboard
        const textarea = document.getElementById('level-json');
        textarea.style.display = 'block';
        textarea.value = json;
        textarea.select();
        
        // Show download button
        const downloadBtn = document.getElementById('download-json-btn');
        if (downloadBtn) {
            downloadBtn.style.display = 'block';
            // Store JSON data for download
            downloadBtn.dataset.json = json;
        }
        
        navigator.clipboard.writeText(json).then(() => {
            alert('Level JSON copied to clipboard!');
        });
    }
    
    shareLevel() {
        const levelData = this.exportLevelData();
        
        // 레벨 이름과 설명 입력받기
        const name = prompt('레벨 이름을 입력하세요:', 'My Custom Level');
        if (!name) return; // 취소한 경우
        
        const description = prompt('레벨 설명을 입력하세요 (선택사항):', '');
        
        // 서버에 저장
        const saveBtn = document.getElementById('share-level-btn');
        saveBtn.disabled = true;
        saveBtn.textContent = '저장 중...';
        
        fetch('api_save_level.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                name: name,
                description: description,
                level_data: levelData
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const fullURL = window.location.origin + data.url;
                navigator.clipboard.writeText(fullURL).then(() => {
                    alert('레벨이 저장되었습니다!\n\n공유 URL이 클립보드에 복사되었습니다:\n' + fullURL);
                }).catch(() => {
                    prompt('아래 URL을 복사하세요:', fullURL);
                });
            } else {
                alert('레벨 저장 실패: ' + (data.error || '알 수 없는 오류'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('레벨 저장 중 오류가 발생했습니다.');
        })
        .finally(() => {
            saveBtn.disabled = false;
            saveBtn.textContent = '🔗 공유 URL 생성';
        });
    }
    
    importLevel() {
        const json = prompt('레벨 JSON 데이터를 입력하세요:');
        if (json) {
            try {
                const levelData = JSON.parse(json);
                this.game.loadCustomLevel(levelData);
                alert('레벨이 성공적으로 불러와졌습니다!');
            } catch (e) {
                alert('잘못된 JSON 형식입니다.');
            }
        }
    }
    
    exportLevelData() {
        const switches = [];
        const walls = [];
        const toggleBlocks = [];
        let goal = null;
        let startPosition = this.startPosition || { x: 1, y: 12 };
        
        for (let y = 0; y < this.game.gridSize; y++) {
            for (let x = 0; x < this.game.gridSize; x++) {
                const cell = this.game.board[y][x];
                
                if (cell.type === 'wall') {
                    walls.push({x, y});
                } else if (cell.type === 'switch' && cell.switchData) {
                    switches.push(cell.switchData);
                } else if (cell.type === 'toggle-block') {
                    toggleBlocks.push({x, y});
                } else if (cell.type === 'goal') {
                    goal = {x, y};
                } else if (cell.type === 'start') {
                    startPosition = {x, y};
                }
            }
        }
        
        return { switches, walls, toggleBlocks, goal, startPosition };
    }
}

class PuzzleGame {
    // 물리 설정 상수 (튜닝 용이성을 위해 분리)
    static PHYSICS = {
        MOVE_SPEED: 0.05,       // 이동 속도
        JUMP_VELOCITY: -0.155,  // 점프 초기 속도 (1칸 점프, 중력에 맞춰 조정)
        GRAVITY: 0.01,          // 중력 (낮춰서 체공시간 증가)
        MAX_FALL_SPEED: 0.25,   // 최대 낙하 속도
        FRICTION: 0.8,          // 마찰력
        PLAYER_SIZE: 0.8        // 플레이어 크기 (셀 기준)
    };
    
    constructor() {
        this.gridSize = 15;
        this.board = [];
        // 실수 좌표로 관리 (서브픽셀 움직임)
        this.player = { x: 1, y: 12 };
        this.velocity = { x: 0, y: 0 };
        this.currentLevel = 1;
        this.isJumping = false;
        this.gameWon = false;
        this.editor = null;
        this.levels = [];
        this.levelsLoaded = false;
        this.disableAutoLevelLoad = Boolean(window.disableAutoLevelLoad);
        this.disableURLUpdates = this.disableAutoLevelLoad;
        
        // 물리 상수 (정적 상수에서 참조)
        this.physics = PuzzleGame.PHYSICS;
        
        this.ready = this.init();
    }
    
    async loadLevelsFromJSON() {
        try {
            // Try to load individual level files
            this.levels = [];
            let levelNum = 1;
            
            while (true) {
                try {
                    const response = await fetch(`levels/level${levelNum}.json`);
                    if (!response.ok) break;
                    const levelData = await response.json();
                    this.levels.push(levelData);
                    levelNum++;
                } catch {
                    break;
                }
            }
            
            if (this.levels.length === 0) {
                throw new Error('No level files found');
            }
            
            this.levelsLoaded = true;
            console.log(`${this.levels.length} levels loaded.`);
            return true;
        } catch (error) {
            console.error('Level loading failed:', error);
            // Fallback default level
            this.levels = [{
                name: "Default Level",
                startPosition: { x: 1, y: 12 },
                switches: [],
                walls: Array.from({length: 15}, (_, i) => ({x: i, y: 14})),
                toggleBlocks: [],
                goal: { x: 14, y: 13 }
            }];
            this.levelsLoaded = true;
            return false;
        }
    }
    
    async init() {
        this.createBoard();
        await this.loadLevelsFromJSON();
        this.bindEvents();
        this.startGameLoop();
        this.editor = new LevelEditor(this);
        
        // URL 라우팅 초기화
        this.initRouter();
    }
    
    initRouter() {
        // URL 변경 감지
        window.addEventListener('hashchange', () => {
            this.handleURLChange();
        });
        
        // 초기 URL 파싱
        const route = this.parseURL();
        
        switch (route.type) {
            case 'editor':
                this.loadLevel(1);
                // 약간의 딜레이 후 에디터 열기 (DOM 준비 대기)
                setTimeout(() => {
                    if (!this.editor.isActive) {
                        this.editor.toggle();
                    }
                }, 100);
                break;
            case 'level':
                this.currentLevel = route.level;
                this.loadLevel(this.currentLevel);
                break;
            case 'custom':
                this.loadCustomLevel(route.levelData);
                break;
            case 'none':
                break;
        }
    }

    loadCustomLevel(levelData) {
        // 유효성 검사
        if (!levelData || typeof levelData !== 'object') {
            console.error('Invalid level data: levelData is null or not an object');
            return false;
        }
        if (!Array.isArray(levelData.walls)) {
            console.error('Invalid level data: walls must be an array');
            return false;
        }
        if (!Array.isArray(levelData.switches)) {
            console.error('Invalid level data: switches must be an array');
            return false;
        }
        
        // 보드 초기화
        for (let y = 0; y < this.gridSize; y++) {
            for (let x = 0; x < this.gridSize; x++) {
                const cell = this.board[y][x];
                cell.type = 'empty';
                cell.element.className = 'cell';
                cell.element.innerHTML = '';
                cell.switchData = null;
                cell.wallActive = false;
                cell.isToggleBlock = false;
            }
        }
        
        // 벽 설정
        levelData.walls.forEach(wall => {
            const cell = this.board[wall.y][wall.x];
            cell.type = 'wall';
            cell.element.classList.add('wall');
        });
        
        // 스위치 설정
        levelData.switches.forEach(switchData => {
            const cell = this.board[switchData.y][switchData.x];
            cell.type = 'switch';
            cell.switchData = switchData;
            cell.element.classList.add('switch');
        });
        
        // 토글 블록 설정
        if (levelData.toggleBlocks) {
            levelData.toggleBlocks.forEach(block => {
                const cell = this.board[block.y][block.x];
                cell.type = 'toggle-block';
                cell.isToggleBlock = true;
                cell.element.classList.add('toggle-block');
            });
        }
        
        // 목표 설정
        if (levelData.goal) {
            const goalCell = this.board[levelData.goal.y][levelData.goal.x];
            goalCell.type = 'goal';
            goalCell.element.classList.add('goal');
            goalCell.element.innerHTML = '🏁';
        }
        
        // 시작점 설정 (에디터에서 인식할 수 있도록 - 내부 타입만 설정)
        const start = levelData.startPosition || { x: 1, y: 12 };
        const startCell = this.board[start.y][start.x];
        startCell.type = 'start';
        // 에디터 모드에서만 시각적 표시
        if (this.editor && this.editor.isActive) {
            startCell.element.classList.add('start-point');
            startCell.element.innerHTML = '🧙‍♂️';
        }
        
        // 플레이어 위치 초기화 (커스텀 시작점 사용)
        this.player = { x: start.x, y: start.y };
        this.velocity = { x: 0, y: 0 };
        this.isJumping = false;
        this.lastSwitchPos = null;
        this.updatePlayerPosition();
        
        // UI 업데이트 (요소가 있는 경우만)
        const levelElement = document.getElementById('current-level');
        if (levelElement) {
            levelElement.textContent = 'Custom';
        }
        this.gameWon = false;
        const nextBtn = document.getElementById('next-level-btn');
        if (nextBtn) {
            nextBtn.style.display = 'none';
        }
        const victoryMsg = document.getElementById('victory-message');
        if (victoryMsg) {
            victoryMsg.style.display = 'none';
        }
    }
    
    createBoard() {
        const gameBoard = document.getElementById('game-board');
        gameBoard.innerHTML = '';
        
        for (let y = 0; y < this.gridSize; y++) {
            this.board[y] = [];
            for (let x = 0; x < this.gridSize; x++) {
                const cell = document.createElement('div');
                cell.className = 'cell';
                cell.dataset.x = x;
                cell.dataset.y = y;
                gameBoard.appendChild(cell);
                this.board[y][x] = { type: 'empty', element: cell };
            }
        }
        
        // 플레이어 스프라이트 생성
        this.playerSprite = document.createElement('div');
        this.playerSprite.className = 'player-sprite';
        gameBoard.appendChild(this.playerSprite);
    }
    
    loadLevel(levelNum) {
        if (levelNum > this.levels.length) {
            this.showVictory("모든 레벨 완료!");
            return;
        }
        
        // 보드 초기화
        for (let y = 0; y < this.gridSize; y++) {
            for (let x = 0; x < this.gridSize; x++) {
                const cell = this.board[y][x];
                cell.type = 'empty';
                cell.element.className = 'cell';
                cell.element.innerHTML = '';
                cell.switchData = null;
                cell.wallActive = false;
                cell.isToggleBlock = false;
            }
        }
        
        const level = this.levels[levelNum - 1];
        
        // 벽 설정
        level.walls.forEach(wall => {
            const cell = this.board[wall.y][wall.x];
            cell.type = 'wall';
            cell.element.classList.add('wall');
        });
        
        // 스위치 설정
        level.switches.forEach(switchData => {
            const cell = this.board[switchData.y][switchData.x];
            cell.type = 'switch';
            cell.switchData = switchData;
            cell.element.classList.add('switch');
        });
        
        // 토글 블록 설정
        if (level.toggleBlocks) {
            level.toggleBlocks.forEach(block => {
                const cell = this.board[block.y][block.x];
                cell.type = 'toggle-block';
                cell.isToggleBlock = true;
                cell.element.classList.add('toggle-block');
                // 초기에는 숨김 상태
                cell.element.classList.add('hidden');
            });
        }
        
        // 목표 설정
        if (level.goal && level.goal.x !== undefined && level.goal.y !== undefined) {
            const goalCell = this.board[level.goal.y][level.goal.x];
            goalCell.type = 'goal';
            goalCell.element.classList.add('goal');
            goalCell.element.innerHTML = '🏁';
        }
        
        // 시작점 설정 (에디터에서 인식할 수 있도록 - 내부 타입만 설정)
        const start = level.startPosition || { x: 1, y: 12 };
        const startCell = this.board[start.y][start.x];
        startCell.type = 'start';
        // 에디터 모드에서만 시각적 표시
        if (this.editor && this.editor.isActive) {
            startCell.element.classList.add('start-point');
            startCell.element.innerHTML = '🧙‍♂️';
        }
        
        // 플레이어 위치 초기화 (레벨의 시작점 사용)
        this.player = { x: start.x, y: start.y };
        this.velocity = { x: 0, y: 0 };
        this.isJumping = false;
        this.lastSwitchPos = null;
        this.updatePlayerPosition();
        
        // 레벨 이름 표시 (있는 경우)
        const levelText = level.name ? `${levelNum}. ${level.name}` : levelNum;
        const levelElement = document.getElementById('current-level');
        if (levelElement) {
            levelElement.textContent = levelText;
        }
        this.gameWon = false;
        const nextBtn = document.getElementById('next-level-btn');
        if (nextBtn) {
            nextBtn.style.display = 'none';
        }
        const victoryMsg = document.getElementById('victory-message');
        if (victoryMsg) {
            victoryMsg.style.display = 'none';
        }
        
        // URL 업데이트 (에디터 모드가 아닐 때만)
        if (!this.editor || !this.editor.isActive) {
            this.updateURL('level/' + levelNum);
        }
    }
    
    bindEvents() {
        // 키 상태 추적
        this.keysPressed = {};
        this.moveInterval = null;
        this.lastMoveTime = 0;
        this.moveDelay = 80; // ms 사이 이동 딜레이
        
        // 키보드 이벤트
        document.addEventListener('keydown', (e) => {
            if (this.gameWon) return;
            
            const key = e.code;
            
            // 점프는 키 다운 시 즉시 실행 (한 번만)
            if (key === 'KeyW' || key === 'ArrowUp' || key === 'Space') {
                e.preventDefault();
                if (!this.keysPressed[key]) {
                    this.keysPressed[key] = true;
                    this.jump();
                }
            }
            
            // 이동 키 등록
            if (key === 'KeyA' || key === 'ArrowLeft' || key === 'KeyD' || key === 'ArrowRight' || key === 'KeyS' || key === 'ArrowDown') {
                e.preventDefault();
                if (!this.keysPressed[key]) {
                    this.keysPressed[key] = true;
                    this.processMovement(); // 즉시 첫 이동
                    this.startContinuousMovement();
                }
            }
        });
        
        document.addEventListener('keyup', (e) => {
            const key = e.code;
            this.keysPressed[key] = false;
            
            // 모든 이동 키가 떼어졌으면 연속 이동 중지
            const moveKeys = ['KeyA', 'ArrowLeft', 'KeyD', 'ArrowRight', 'KeyS', 'ArrowDown'];
            const anyMoveKeyPressed = moveKeys.some(k => this.keysPressed[k]);
            if (!anyMoveKeyPressed) {
                this.stopContinuousMovement();
            }
        });
        
        // 포커스 잃으면 모든 키 해제
        window.addEventListener('blur', () => {
            this.keysPressed = {};
            this.stopContinuousMovement();
        });
        
        // 버튼 이벤트
        const resetBtn = document.getElementById('reset-btn');
        if (resetBtn) {
            resetBtn.addEventListener('click', () => {
                this.loadLevel(this.currentLevel);
            });
        }
        
        const nextBtn = document.getElementById('next-level-btn');
        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                this.currentLevel++;
                this.loadLevel(this.currentLevel);
            });
        }
        
        const continueBtn = document.getElementById('continue-btn');
        if (continueBtn) {
            continueBtn.addEventListener('click', () => {
                // 테스트 플레이 모드면 에디터로 돌아가기
                if (this.editor && this.editor.isTestPlaying) {
                    this.editor.returnToEditor();
                    return;
                }
                
                document.getElementById('victory-message').style.display = 'none';
                this.currentLevel++;
                this.loadLevel(this.currentLevel);
            });
        }
    }
    
    jump() {
        if (this.isOnGround() && !this.isJumping) {
            this.isJumping = true;
            this.velocity.y = this.physics.JUMP_VELOCITY;
            
            if (this.playerSprite) {
                this.playerSprite.classList.add('jumping');
                this.playerSprite.classList.remove('falling');
            }
        }
    }
    
    processMovement() {
        if (this.gameWon) return;
        
        const now = Date.now();
        if (now - this.lastMoveTime < this.moveDelay) return;
        this.lastMoveTime = now;
        
        // 왼쪽 이동
        if (this.keysPressed['KeyA'] || this.keysPressed['ArrowLeft']) {
            const targetX = this.player.x - this.physics.MOVE_SPEED;
            if (!this.checkCollision(targetX, this.player.y)) {
                this.player.x = targetX;
            }
        }
        
        // 오른쪽 이동
        if (this.keysPressed['KeyD'] || this.keysPressed['ArrowRight']) {
            const targetX = this.player.x + this.physics.MOVE_SPEED;
            if (!this.checkCollision(targetX, this.player.y)) {
                this.player.x = targetX;
            }
        }
        
        // 아래 이동 (빠른 낙하)
        if (this.keysPressed['KeyS'] || this.keysPressed['ArrowDown']) {
            const targetY = this.player.y + this.physics.MOVE_SPEED;
            if (!this.checkCollision(this.player.x, targetY)) {
                this.player.y = targetY;
            }
        }
        
        this.updatePlayerPosition();
    }
    
    startContinuousMovement() {
        if (this.moveInterval) return;
        
        this.moveInterval = setInterval(() => {
            this.processMovement();
        }, this.moveDelay);
    }
    
    stopContinuousMovement() {
        if (this.moveInterval) {
            clearInterval(this.moveInterval);
            this.moveInterval = null;
        }
    }
    
    // 특정 그리드 셀이 벽인지 확인
    isWall(gridX, gridY) {
        if (gridX < 0 || gridX >= this.gridSize || gridY < 0 || gridY >= this.gridSize) {
            return true; // 경계 밖은 벽
        }
        const cell = this.board[gridY][gridX];
        if (cell.type === 'wall' || cell.wallActive) {
            return true;
        }
        if (cell.type === 'toggle-block' && !cell.element.classList.contains('hidden')) {
            return true;
        }
        return false;
    }
    
    // 플레이어 바운딩 박스가 벽과 충돌하는지 확인
    checkCollision(x, y) {
        const playerSize = this.physics.PLAYER_SIZE;
        const offset = (1 - playerSize) / 2;
        
        // 플레이어 경계 (약간 작게)
        const left = x + offset;
        const right = x + 1 - offset;
        const top = y + offset;
        const bottom = y + 1 - offset;
        
        // 플레이어가 차지하는 모든 셀 확인
        const minCellX = Math.floor(left);
        const maxCellX = Math.floor(right - 0.001);
        const minCellY = Math.floor(top);
        const maxCellY = Math.floor(bottom - 0.001);
        
        for (let cy = minCellY; cy <= maxCellY; cy++) {
            for (let cx = minCellX; cx <= maxCellX; cx++) {
                if (this.isWall(cx, cy)) {
                    // 벽의 높이의 위에서부터 80%까지만 충돌 판정
                    // 벽의 상단 80% 영역 (cy부터 cy + 0.8까지)
                    const wallTop = cy;
                    const wallCollisionBottom = cy + 0.8;
                    
                    // 플레이어의 상단이 벽의 충돌 영역 내에 있는지 확인
                    if (top < wallCollisionBottom) {
                        return true;
                    }
                }
            }
        }
        return false;
    }
    
    // 구 버전 호환용
    canMoveTo(x, y) {
        return !this.isWall(Math.floor(x), Math.floor(y));
    }
    
    isOnGround() {
        // 플레이어 바로 아래에 벽이 있는지 체크
        const playerSize = this.physics.PLAYER_SIZE;
        const offset = (1 - playerSize) / 2;
        // 플레이어의 실제 아래쪽 경계 + 약간의 여유
        const bottom = this.player.y + 1 - offset;
        const checkY = bottom + 0.05; // 발 밑으로 조금 아래를 체크
        const left = this.player.x + offset;
        const right = this.player.x + 1 - offset - 0.001;
        
        const minCellX = Math.floor(left);
        const maxCellX = Math.floor(right);
        const cellY = Math.floor(checkY);
        
        // 경계를 벗어나면 땅이 아님
        if (cellY >= this.gridSize) {
            return false;
        }
        
        for (let cx = minCellX; cx <= maxCellX; cx++) {
            if (this.isWall(cx, cellY)) {
                return true;
            }
        }
        return false;
    }
    
    updatePlayerPosition() {
        // 스프라이트 위치 업데이트 (서브픽셀)
        if (this.playerSprite) {
            const cellSize = 100 / this.gridSize;
            this.playerSprite.style.left = `${this.player.x * cellSize}%`;
            this.playerSprite.style.top = `${this.player.y * cellSize}%`;
        }
    }
    
    checkSwitch() {
        // 플레이어 중심 위치 기준
        const centerX = Math.floor(this.player.x + 0.5);
        const centerY = Math.floor(this.player.y + 0.5);
        
        if (centerX < 0 || centerX >= this.gridSize || centerY < 0 || centerY >= this.gridSize) return;
        
        const cell = this.board[centerY][centerX];
        
        if (cell.type === 'switch') {
            // 이미 이 스위치 위에 있었으면 무시 (한 번만 토글)
            if (this.lastSwitchPos && this.lastSwitchPos.x === centerX && this.lastSwitchPos.y === centerY) {
                return;
            }
            
            // 스위치 토글
            const isActive = cell.element.classList.contains('active');
            
            if (isActive) {
                // 비활성화
                cell.element.classList.remove('active');
                // 토글블록 숨기기
                cell.switchData.blocks.forEach(block => {
                    const blockCell = this.board[block.y][block.x];
                    blockCell.element.classList.add('hidden');
                });
            } else {
                // 활성화
                cell.element.classList.add('active');
                // 토글블록 표시
                cell.switchData.blocks.forEach(block => {
                    const blockCell = this.board[block.y][block.x];
                    blockCell.element.classList.remove('hidden');
                });
            }
            
            this.lastSwitchPos = { x: centerX, y: centerY };
        } else {
            // 스위치에서 벗어나면 위치 초기화
            this.lastSwitchPos = null;
        }
    }
    
    checkGoal() {
        // 플레이어 중심 위치 기준
        const centerX = Math.floor(this.player.x + 0.5);
        const centerY = Math.floor(this.player.y + 0.5);
        
        if (centerX < 0 || centerX >= this.gridSize || centerY < 0 || centerY >= this.gridSize) return;
        
        const currentCell = this.board[centerY][centerX];
        
        if (currentCell.type === 'goal') {
            this.gameWon = true;
            setTimeout(() => {
                this.showVictory();
            }, 300);
        }
    }
    
    showVictory(message = "레벨 클리어!") {
        document.getElementById('victory-message').style.display = 'flex';
        document.querySelector('.victory-content h2').textContent = `🎉 ${message}`;
        
        // 테스트 플레이 모드면 에디터로 돌아가기 버튼 표시
        if (this.editor && this.editor.isTestPlaying) {
            document.getElementById('continue-btn').textContent = '에디터로 돌아가기';
        } else if (this.currentLevel < this.levels.length) {
            document.getElementById('continue-btn').textContent = '다음 레벨';
        } else {
            document.getElementById('continue-btn').textContent = '게임 완료';
        }
    }
    
    startGameLoop() {
        const loop = () => {
            if (this.gameWon) {
                requestAnimationFrame(loop);
                return;
            }
            
            // 입력 처리 - 좌우 이동
            if (this.keysPressed['KeyA'] || this.keysPressed['ArrowLeft']) {
                this.velocity.x = -this.physics.MOVE_SPEED;
            } else if (this.keysPressed['KeyD'] || this.keysPressed['ArrowRight']) {
                this.velocity.x = this.physics.MOVE_SPEED;
            } else {
                // 키를 떼면 즉시 멈춤
                this.velocity.x = 0;
            }
            
            // 아래키 - 빠른 낙하
            if (this.keysPressed['KeyS'] || this.keysPressed['ArrowDown']) {
                this.velocity.y += this.physics.GRAVITY * 2;
            }
            
            // 중력 적용
            this.velocity.y += this.physics.GRAVITY;
            if (this.velocity.y > this.physics.MAX_FALL_SPEED) {
                this.velocity.y = this.physics.MAX_FALL_SPEED;
            }
            
            // 수평 이동 + 충돌
            const playerSize = this.physics.PLAYER_SIZE;
            const playerOffset = (1 - playerSize) / 2;
            
            let newX = this.player.x + this.velocity.x;
            if (!this.checkCollision(newX, this.player.y)) {
                this.player.x = newX;
            } else {
                // 벽에 부딧힘 - 위치 조정
                if (this.velocity.x > 0) {
                    // 오른쪽 벽 - 플레이어 오른쪽 경계가 벽 왼쪽에 맞닿음
                    const rightEdge = this.player.x + 1 - playerOffset;
                    const wallLeft = Math.floor(rightEdge + this.velocity.x);
                    this.player.x = wallLeft - 1 + playerOffset - 0.001;
                } else if (this.velocity.x < 0) {
                    // 왼쪽 벽 - 플레이어 왼쪽 경계가 벽 오른쪽에 맞닿음
                    const leftEdge = this.player.x + playerOffset;
                    const wallRight = Math.ceil(leftEdge + this.velocity.x);
                    this.player.x = wallRight - playerOffset + 0.001;
                }
                this.velocity.x = 0;
            }
            
            // 수직 이동 + 충돌
            let newY = this.player.y + this.velocity.y;
            if (!this.checkCollision(this.player.x, newY)) {
                this.player.y = newY;
            } else {
                // 땅/천장에 부딧힘
                if (this.velocity.y > 0) {
                    // 바닥 - 플레이어 아래 경계가 벽 위에 맞닿음
                    const bottomEdge = this.player.y + 1 - playerOffset;
                    const wallTop = Math.floor(bottomEdge + this.velocity.y);
                    this.player.y = wallTop - 1 + playerOffset - 0.001;
                    this.isJumping = false;
                    if (this.playerSprite) {
                        this.playerSprite.classList.remove('falling');
                        this.playerSprite.classList.remove('jumping');
                    }
                } else if (this.velocity.y < 0) {
                    // 천장 - 플레이어 위 경계가 벽 아래에 맞닿음
                    const topEdge = this.player.y + playerOffset;
                    const wallBottom = Math.ceil(topEdge + this.velocity.y);
                    this.player.y = wallBottom - playerOffset + 0.001;
                }
                this.velocity.y = 0;
            }
            
            // 경계 체크
            if (this.player.x < 0) this.player.x = 0;
            if (this.player.x > this.gridSize - 1) this.player.x = this.gridSize - 1;
            if (this.player.y < 0) this.player.y = 0;
            if (this.player.y > this.gridSize - 1) {
                // 화면 밖으로 떨어짐 - 리셋
                this.loadLevel(this.currentLevel);
                return;
            }
            
            // 점프/낙하 애니메이션
            if (this.velocity.y < 0) {
                if (this.playerSprite) {
                    this.playerSprite.classList.add('jumping');
                    this.playerSprite.classList.remove('falling');
                }
            } else if (this.velocity.y > 0.1) {
                if (this.playerSprite) {
                    this.playerSprite.classList.remove('jumping');
                    this.playerSprite.classList.add('falling');
                }
            }
            
            // 위치 업데이트
            this.updatePlayerPosition();
            this.checkSwitch();
            this.checkGoal();
            
            requestAnimationFrame(loop);
        };
        
        requestAnimationFrame(loop);
    }
    
    // URL 라우팅
    updateURL(path) {
        if (this.disableURLUpdates) return;
        window.history.pushState(null, '', '#' + path);
    }
    
    parseURL() {
        const hash = window.location.hash.slice(1); // # 제거
        if (!hash) {
            if (this.disableAutoLevelLoad) {
                return { type: 'none' };
            }
            return { type: 'level', level: 1 };
        }
        
        if (hash === 'editor') {
            return { type: 'editor' };
        }
        
        if (hash.startsWith('level/')) {
            const level = parseInt(hash.split('/')[1]);
            return { type: 'level', level: isNaN(level) ? 1 : level };
        }
        
        if (hash.startsWith('custom/')) {
            const encoded = hash.slice(7);
            try {
                const json = decodeURIComponent(atob(encoded));
                const levelData = JSON.parse(json);
                return { type: 'custom', levelData };
            } catch (e) {
                console.error('Invalid custom level URL:', e);
                return { type: 'level', level: 1 };
            }
        }
        
        return { type: 'level', level: 1 };
    }
    
    handleURLChange() {
        const route = this.parseURL();
        
        switch (route.type) {
            case 'editor':
                if (!this.editor.isActive) {
                    this.editor.toggle();
                }
                break;
            case 'level':
                if (this.editor.isActive) {
                    this.editor.toggle(); // 종료
                }
                if (route.level !== this.currentLevel) {
                    this.currentLevel = route.level;
                    this.loadLevel(this.currentLevel);
                }
                break;
            case 'custom':
                if (this.editor.isActive) {
                    this.editor.toggle(); // 종료
                }
                this.loadCustomLevel(route.levelData);
                break;
            case 'none':
                break;
        }
    }
    
    generateShareURL(levelData) {
        const json = JSON.stringify(levelData);
        const encoded = btoa(encodeURIComponent(json));
        return window.location.origin + window.location.pathname + '#custom/' + encoded;
    }
}

// 게임 시작
document.addEventListener('DOMContentLoaded', () => {
    const game = new PuzzleGame();
    window.game = game; // 전역 접근 가능하도록
    window.gameIsReady = false;

    const readyPromise = game.ready instanceof Promise ? game.ready : Promise.resolve();

    readyPromise.then(() => {
        window.gameIsReady = true;

        if (window.sharedLevelData) {
            game.loadCustomLevel(window.sharedLevelData);
        }

        // 게임 준비 이벤트 발생
        window.dispatchEvent(new Event('gameReady'));
    });
});