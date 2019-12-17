import Register from '../../common/register';

/*
    Why?
 */
export default class TargetBlank {

    constructor ($container) {
        $container.filterAllNodes('a[target="_blank"]').each((idx, item) => this.bind(item));
    }

    bind (item) {
        $(item).on('click', function () {
            const href = $(this).attr('href');
            window.open(href);
            return false;
        });
    }

    static init ($container) {
        // eslint-disable-next-line no-new
        new TargetBlank($container);
    }
}

(new Register()).registerCallback(TargetBlank.init);
