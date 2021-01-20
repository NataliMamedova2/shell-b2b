import React from "react";
import "./styles.scss";

const ReadonlyInput = ({value }: { value: string } ) => {

	return (
		<input
			className="c-input c-input--readonly"
			value={value}
			readOnly={true}
			type="text"
			placeholder="" />
	);
};

export default ReadonlyInput;
