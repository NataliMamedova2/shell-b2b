import React from "react";
import "./styles.scss";
import useFileLoader from "../../hooks/useFileLoader";
import PendingIcon from "../Icon/PendingIcon";
import Icon from "../Icon";
import {TFile} from "../../modules/Documents/types";

type Props = {
	file: TFile
}

const FileLink = ({file}: Props) => {
	const { link, name: title } = file;
	const { pending, loadFile } = useFileLoader(link, title);

	const clickHandler = async () => {
		if(!pending) {
			await loadFile();
		}
	};

	const firstPart = title.slice(0, title.length - 7);
	const lastPart = title.slice(title.length - 7);

	return (
		<div title={title}
		     className="c-styled-link"
		     role="button"
		     onClick={clickHandler}
		>
			<span className="c-styled-link__ellipse">
				{ firstPart }
			</span>
			{ lastPart }
			{ !pending ? <Icon type="export" /> : <PendingIcon/> }
		</div>
	);
};

export default FileLink;
