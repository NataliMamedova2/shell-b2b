'use strict';

import Url from 'url-parse';
import queryString from 'query-string';

const tabsElement = document.querySelector('ul.nav-tabs');

if (tabsElement) {
    const tabs = tabsElement.querySelectorAll('li');

    tabs.forEach(function (element) {
        element.addEventListener("click", (e) => {
            const clickedElem = e.target;
            const parent = clickedElem.closest('li');

            let selector = parent.querySelector('a').getAttribute("href");
            selector = selector.substr(1);

            const form = document.querySelector('form');

            let formAction = form.getAttribute('action');
            if (!formAction) {
                formAction = document.location.pathname;
                form.setAttribute('action', formAction);
            }
            const formActionUrl = new Url(formAction);
            let query = queryString.parse(formActionUrl.query);

            const tabQueryParams = {'active-tab': selector};

            query = {...query, ...tabQueryParams};
            formActionUrl.set('query', query);

            const newUrl = decodeURIComponent(formActionUrl.toString());
            form.setAttribute('action', newUrl);

            const localeSwitcher = document.querySelector('.locale_switcher');
            if (localeSwitcher) {
                localeSwitcher.querySelectorAll('a').forEach(function (element) {
                    const href = element.getAttribute("href");

                    const localeSwitcherUrl = new Url(href);

                    let query = queryString.parse(localeSwitcherUrl.query);
                    query = {...query, ...tabQueryParams};

                    localeSwitcherUrl.set('query', query);
                    element.setAttribute('href', decodeURIComponent(localeSwitcherUrl.toString()));

                });
            }
        });
    });
}
