function setupDOM() {
    document.body.innerHTML = `
        <div id="game-board"></div>
        <div id="victory-message" style="display:none;">
            <div class="victory-content"><h2></h2></div>
        </div>
        <button id="continue-btn"></button>
        <div id="current-level"></div>
        <button id="next-level-btn"></button>
        <button id="reset-btn"></button>
    `;
}

async function createGameInstance() {
    setupDOM();
    window.disablePuzzleGameAutoInit = true;
    window.disableAutoLevelLoad = true;
    global.fetch = jest.fn().mockResolvedValue({ ok: false });
    const { PuzzleGame } = require('../puzzle_game.js');
    const game = new PuzzleGame();
    await game.ready;
    return game;
}

function basicLevel({ start }) {
    return {
        name: 'Test Level',
        startPosition: start,
        switches: [],
        walls: [],
        toggleBlocks: [],
        goal: { x: start.x, y: start.y },
        movingBlocks: {
            horizontal: [{ x: start.x, y: start.y, direction: 1, speed: 0.03 }],
            vertical: []
        },
        eventBlocks: []
    };
}

beforeEach(() => {
    jest.resetModules();
});

test('player rides horizontal moving block without detaching when unobstructed', async () => {
    const game = await createGameInstance();
    const levelData = basicLevel({ start: { x: 3, y: 5 } });
    game.loadCustomLevel(levelData);
    const block = game.movingBlocks[0];
    game.alignPlayerOnMovingBlock(block);
    const originalX = game.player.x;
    game.playerOnMovingBlock = block;
    block.prevX = block.x;
    block.prevY = block.y;
    block.x += 0.2;
    block.deltaX = 0.2;
    block.deltaY = 0;
    game.applyMovingBlockCarry();
    expect(game.player.x).toBeCloseTo(originalX + 0.2, 5);
    expect(game.playerOnMovingBlock).toBe(block);
});

test('large offsets cause moving block reversal and detach the player', async () => {
    const game = await createGameInstance();
    const levelData = basicLevel({ start: { x: 5, y: 7 } });
    game.loadCustomLevel(levelData);
    const block = game.movingBlocks[0];
    block.prevX = block.x;
    block.prevY = block.y;
    block.dir = 1;
    block.deltaX = 0.25;
    game.player.x = block.x + game.constructor.MOVING_BLOCK_REVERSAL_THRESHOLD + 0.2;
    game.positionMovingBlockElement = jest.fn();
    const handled = game.handleMovingBlockCarryCollision(block, 'x', block.deltaX);
    expect(handled).toBe('reversed-detach');
    expect(block.dir).toBe(-1);
    expect(game.playerOnMovingBlock).toBeNull();
});

test('vertical moving block reversing on ceiling keeps rider attached', async () => {
    const game = await createGameInstance();
    const levelData = {
        name: 'Vertical Block Level',
        startPosition: { x: 5, y: 2 },
        switches: [],
        walls: [],
        toggleBlocks: [],
        goal: { x: 5, y: 2 },
        movingBlocks: {
            horizontal: [],
            vertical: [{ x: 5, y: 1, direction: -1, speed: 0.03 }]
        },
        eventBlocks: []
    };
    game.loadCustomLevel(levelData);
    const block = game.movingBlocks.find(b => b.type === 'vertical');
    expect(block).toBeDefined();
    game.alignPlayerOnMovingBlock(block);
    game.playerOnMovingBlock = block;
    block.prevY = block.y;
    block.prevX = block.x;
    block.deltaX = 0;
    block.deltaY = -0.2;
    block.y += block.deltaY;
    block.dir = -1;
    game.positionMovingBlockElement = jest.fn();
    const originalCheck = game.checkCollision.bind(game);
    const checkSpy = jest.spyOn(game, 'checkCollision').mockImplementation((x, y, options) => {
        if (y < 0.5) {
            return true;
        }
        return originalCheck(x, y, options);
    });
    game.applyMovingBlockCarry();
    expect(block.dir).toBe(1);
    expect(game.playerOnMovingBlock).toBe(block);
    checkSpy.mockRestore();
});
