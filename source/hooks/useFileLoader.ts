import {useState} from "react";
import {get} from "../libs";
import {saveAs} from "file-saver";

const useFileLoader = (
	endpoint: string,
	filename: string
) => {
	const [pending, setPending] = useState<boolean>(false);

	const loadFile = async () => {
		setPending(true);

		try {
			const res = await get({
				endpoint: endpoint,
				responseType: "blob"
			}, false);
			setPending(false);
			saveAs(res.data, filename);
		} catch (e) {}
	};

	return { pending, loadFile };
};

export default useFileLoader;
