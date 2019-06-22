class Index {
    constructor() {
        this.initPages();

        getId('content').innerHTML = this.page.get();

        window.ComponentRenderComplete = true;
        document.dispatchEvent(new CustomEvent('ComponentRenderComplete'));
    }

    initPages() {
        this.page = new IndexChunk('<?=getHTML('../views/Index.php') ?>');
    }
}

class IndexChunk extends Chunk {
    /*------------------------|
     |        FUNCTIONS       |
     |-----------------------*/
    /*-----------------------|
     |    INITIALIZATION     |
     |-----------------------*/
    initClass() {
        this.className = 'IndexChunk';
    }
    initChild() {

    }
    rerenderDom() {
        getId('content').style.paddingTop = '80px';
    }
    destructor() {
        getId('content').style.paddingTop = '';
    }
}