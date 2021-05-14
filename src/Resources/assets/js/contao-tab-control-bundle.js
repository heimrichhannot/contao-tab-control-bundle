import {Tab} from 'bootstrap';

class ContaoTabControlBundle {
    static init() {
        document.querySelectorAll('.ce_tabcontrol').forEach((tabControl, key, parent) => {
            if ('true' === tabControl.dataset.rememberLastTab) {
                let savedTabPosition = sessionStorage.getItem(tabControl.id);
                if (null !== savedTabPosition) {
                    let activeTab = tabControl.querySelector('#' + savedTabPosition);
                    if (null !== activeTab) {
                        (new Tab(activeTab)).show();
                    }
                } else {
                    (new Tab(tabControl.querySelector('a.nav-link'))).show();
                }
            }

            tabControl.querySelectorAll('a.tab-link').forEach((element, key, parent) => {
                element.addEventListener('click', (e) => {
                    e.preventDefault();

                    element.addEventListener('shown.bs.tab', function (event) {
                        sessionStorage.setItem(tabControl.id, e.target.id);
                    });

                    (new Tab(element)).show();
                });
            });
        });
    }
}

document.addEventListener('DOMContentLoaded', ContaoTabControlBundle.init);
