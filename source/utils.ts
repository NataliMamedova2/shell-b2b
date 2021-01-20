import "./vendors/utils-polyfills";

declare global {
	interface Window {
		__i18n__: { [key: string]: string }
	}
}

(() => {
	togglePasswords();
})();

function togglePasswords (){
	const fields: NodeListOf<HTMLSpanElement> = document.querySelectorAll(".c-password");


	const addHandlers = (field: HTMLSpanElement) => {
		const eyeButton: HTMLSpanElement | null = field.querySelector(".c-password__button");
		const eyeIcon: HTMLImageElement | null = field.querySelector(".c-password__icon");
		const input: HTMLInputElement | null = field.querySelector(".c-password__input");

		const { showPassword, hidePassword } = window.__i18n__;

		if(eyeButton) {
			eyeButton.addEventListener("click", (e: Event) => {
				e.stopPropagation();
				e.preventDefault();

				if(!input || !eyeIcon) {
					return false;
				}
				if(input.type === "text") {
					input.type = "password";
					eyeIcon.src = "/media/eye.svg";
					eyeButton.title = showPassword;
				} else  {
					input.type = "text";
					eyeIcon.src = "/media/eye-disable.svg";
					eyeButton.title = hidePassword;
				}
			}, false);
		}

	};
	fields.forEach(addHandlers);
}
