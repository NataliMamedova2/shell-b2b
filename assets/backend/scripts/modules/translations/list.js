'use strict';

const filter = document.querySelector("#filter-container");
const list = document.querySelector("#list-container");

const showEmptyButton = list.querySelector(".show-only-empty");
if (showEmptyButton) {
    showEmptyButton.addEventListener("click", (e) => {
        e.preventDefault();

        e.target.classList.add("active");
        showAllButton.classList.remove("active");

        let elements = list.getElementsByClassName('message');
        for (let i = 0; i < elements.length; ++i) {
            const element = elements[i];
            element.classList.add("hidden");
        }
        elements = list.getElementsByClassName('empty');
        for (let i = 0; i < elements.length; ++i) {
            const element = elements[i];
            element.classList.remove("hidden");
        }
    });
}

const showAllButton = list.querySelector(".show-all");
if (showAllButton) {
    showAllButton.addEventListener("click", (e) => {
        e.preventDefault();

        e.target.classList.add("active");
        showEmptyButton.classList.remove("active");

        const elements = list.getElementsByClassName('message');
        for (let i = 0; i < elements.length; ++i) {
            const element = elements[i];
            element.classList.remove("hidden");
        }
    });
}
