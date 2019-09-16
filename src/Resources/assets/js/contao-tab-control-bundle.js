let $ = require('jquery');
import 'bootstrap/js/dist/tab';

class ContaoTabControlBundle
{
    constructor()
    {
        this.tabGroups = null;
    }

    init()
    {
        document.querySelectorAll('.ce_tabcontrol').forEach((tabControl, key, parent) => {
            if ('true' === tabControl.dataset.rememberLastTab)
            {
                let savedTabPosition = sessionStorage.getItem(tabControl.id);
                if (null !== savedTabPosition)
                {
                    let activeTab = tabControl.querySelector('#' + savedTabPosition);
                    if (null !== activeTab)
                    {
                        $(activeTab).tab('show');
                    }
                }
                else {
                    $(tabControl.querySelector('a.nav-link')).tab('show');
                }
            }

            tabControl.querySelectorAll('a.tab-link').forEach((element, key, parent) => {

                element.addEventListener('click', (e) => {
                    e.preventDefault();

                    $(element).on('shown.bs.tab', function (e) {
                        sessionStorage.setItem(tabControl.id, e.target.id);
                    });
                    $(element).tab('show');
                });
            });
        });
    }
}

let tabControl = new ContaoTabControlBundle();
document.addEventListener('DOMContentLoaded', tabControl.init);