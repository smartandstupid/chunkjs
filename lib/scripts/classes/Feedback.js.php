class Feedback {
    constructor() {
        this.initPages();

        getId('content').innerHTML = this.page.get();

        window.ComponentRenderComplete = true;
        document.dispatchEvent(new CustomEvent('ComponentRenderComplete'));
    }

    initPages() {
        this.page = new FeedbackChunk('<?=getHTML('../views/Feedback.php') ?>');
    }
}

class FeedbackChunk extends Chunk {
    /*------------------------|
     |        FUNCTIONS       |
     |-----------------------*/
    /*-----------------------|
     |    INITIALIZATION     |
     |-----------------------*/
    initClass() {
        this.className = 'FeedbackChunk';
    }
    initChild() {

    }
}