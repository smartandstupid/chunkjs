class PrivateOffice {
    constructor() {
        this.initPages();
        getId('content').innerHTML = this.page.get();
        this.page.contentComplete();
        window.ComponentRenderComplete = true;
        document.dispatchEvent(new CustomEvent('ComponentRenderComplete'));

        this.initSphere();
    }

    initPages() {
        this.page = new PrivateOfficeChunk('<?=getHTML('../views/PrivateOffice.php') ?>');
    }

    initSphere() {
        /*
        |  Функция для отрисовки canvas js сферы
         */
        <?php require('../general/initSphere.js.php') ?>
    }
}

class PrivateOfficeChunk extends Chunk {
    openPage(event) {
        deleteActive(event.target);
        const innerVar = {
            'Личный кабинет' : 'officePage',
            'История сделок' : 'historyPage',
            'Рефералы' : 'referralsPage'
        }
        this.content(({'privateOfficePage' : this[innerVar[event.target.innerHTML]]}));
    }
    initClass() {
        this.className = 'PrivateOfficeChunk';
        this.storage = 'profile';
        this.property({'avatar' : avatar, 'name' : name});
        this.content({'privateOfficePage' : this.officePage});
    }
    initChild() {
        this.officePage = new Chunk('<?=getHTML('../views/PrivateOffice.referrals.php') ?>');

        this.historyPage = new Chunk('<?=getHTML('../views/PrivateOffice.history.php') ?>');

        this.referralsPage = new Chunk('<?=getHTML('../views/PrivateOffice.referrals.php') ?>');
    }
}