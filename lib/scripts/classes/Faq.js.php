class Faq {
    constructor() {
        this.initPages();

        getId('content').innerHTML = this.page.get();

        window.ComponentRenderComplete = true;
        document.dispatchEvent(new CustomEvent('ComponentRenderComplete'));
    }

    initPages() {
        this.page = new FaqChunk('<?=getHTML('../views/Faq.php') ?>');
    }
}

class FaqChunk extends Chunk {
    /*------------------------|
     |        FUNCTIONS       |
     |-----------------------*/
    /*-----------------------|
     |    INITIALIZATION     |
     |-----------------------*/
    initClass() {
        this.className = 'FaqChunk';
    }
    initChild() {

    }
}