import React from "react";
import {useBreakpoint} from "../../libs/Breakpoint";
import NewsWidget from "../../components/NewsWidget";

const HomeNews  = () => {
	const { state: { isTablet } } = useBreakpoint();

	return (
		<div className="m-home__news">
			<NewsWidget count={ isTablet ? 4 : 3} />
		</div>
	);
};

export default HomeNews;
