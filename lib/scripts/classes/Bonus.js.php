class Bonus {
    constructor() {
        this.initPages();

        getId('content').innerHTML = this.page.get();

        window.ComponentRenderComplete = true;
        document.dispatchEvent(new CustomEvent('ComponentRenderComplete'));

        this.initSphere();
    }

    initPages() {
        this.page = new BonusChunk('<?=getHTML('../views/Bonus.php') ?>');
    }

    initSphere() {
        /*
         |  Функция для отрисовки canvas js сферы
         */
        <?php require('../general/initSphere.js.php') ?>
    }
}

class BonusChunk extends Chunk {
    /*------------------------|
     |        FUNCTIONS       |
     |-----------------------*/
    /*-----------------------|
     |    INITIALIZATION     |
     |-----------------------*/
    initClass() {
        this.className = 'BonusChunk';
    }
    initChild() {

    }
}