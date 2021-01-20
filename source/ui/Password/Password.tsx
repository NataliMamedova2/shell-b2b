import React, {useState} from "react";
import "./styles.scss";
import {TSingleInput} from "@app-types/TSingleInput";
import classNames from "classnames";
import {useTranslation} from "react-i18next";

const Password = ({error,value, onChange, options}: TSingleInput) => {
	const { t } = useTranslation();
	const [ active, setActive ] = useState(false);
	const notEmpty = value.length > 0;
	const iconSource = active ? "/media/eye-disable.svg" : "/media/eye.svg";
	const buttonIcon = active ? t("Hide password") : t("Show password");

	const classes = classNames("c-password", {
		"is-error": error
	});

	return (
		<div className={classes}>
			<input
				className="c-password__input"
				onChange={(e) => onChange(e.target.value)}
				placeholder={options.placeholder}
				type={active ? "text" : "password"}
				autoComplete="off"
				value={value}/>

			{ notEmpty && (
				<span className="c-password__button" onClick={() => setActive(!active)} title={buttonIcon}>
					<img alt=" " className="c-password__icon" src={iconSource}/>
				</span>
			) }
		</div>
	);
};

export default Password;
