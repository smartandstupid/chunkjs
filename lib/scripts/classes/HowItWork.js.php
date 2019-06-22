class HowItWork {
    constructor() {
        this.initPages();

        getId('content').innerHTML = this.page.get();
        this.page.contentComplete();
        window.ComponentRenderComplete = true;
        document.dispatchEvent(new CustomEvent('ComponentRenderComplete'));
    }

    initPages() {
        this.page = new HowItWorkChunk('<?=getHTML('../views/HowItWork.php') ?>');
    }
}

class HowItWorkChunk extends Chunk {
    openPage(event) {
        deleteActive(event.target);
        const innerVar = {
            'Инвентарь' : 'officePage',
            'Выкупить' : 'historyPage'
        }
    }
    initClass() {
        this.className = 'HowItWorkChunk';
        this.storage = 'howItWork';
    }
    initChild() {


    }
}