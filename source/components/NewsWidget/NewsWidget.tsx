import React, {useEffect, useState} from "react";
import "./styles.scss";
import Button from "../../ui/Button";
import {H3, Paragraph} from "../../ui/Typography";
import {useTranslation} from "react-i18next";
import axios, {AxiosError} from "axios";
import {captureError} from "../../vendors/exeptions";
import {logger} from "../../libs";
import {DEFAULT_LANGUAGE} from "../../environment";
import {getCurrentLanguage} from "../../config/i18n/getCurrentLanguage";

type TNewsItem = {
	id: number,
	text: string,
	title: string,
	preview: string,
	link: string
}

const NewsItem = ({ item }: { item: TNewsItem }) => {
	return (
		<a className="m-news__item" href={item.link} target="_blank" rel="noopener noreferrer">
			<div className="m-news__media" style={{ backgroundImage: `url(${item.preview})` }} />
			<H3 className="m-news__title">{ item.title }</H3>
			 { item.text && <Paragraph>{ item.text }</Paragraph>}
		</a>
	);
};

const NewsPlaceholder = React.memo(() => (
	<span className="m-news__item m-news__item--placeholder">
		<div className="m-news__media"/>
		<H3 className="m-news__title m-news__title--placeholder">
			<span /><span />
		</H3>
	</span>
));

const LoadingNewsFailed = React.memo(({url}: {url?: string}) => {
	const { t } = useTranslation();
	return (
		<div className="m-news">
			<div className="m-news__empty">
				<H3 color="darkgrey">{ t("Can't load news") }</H3>
				{
					url && <Button href={url}>{ t("See all news on site") }</Button>
				}
			</div>
		</div>
	);
});

const NewsWidget = ({ count }: { count:number }) => {
	const [pending, setPending] = useState<boolean>(true);
	const [error, setError] = useState<boolean>(false);
	const [data, setData] = useState<TNewsItem[]>([]);
	const { t } = useTranslation();
	const url = getCurrentLanguage() === DEFAULT_LANGUAGE
		? process.env.MARKETING_NEWS_PAGE
		: process.env.MARKETING_NEWS_PAGE_EN;


	useEffect(() => {
		let isCanceled = false;
		setPending(true);
		setError(false);
		const newsUrl = `${process.env.MARKETING_API_NEWS_HOST}/api/v1/news?limit=3&page=1`;

		axios
			.get(newsUrl)
			.then(res => {

				if(!isCanceled) {
					setData(res.data);
					setError(false);
				}
			})
			.catch((e: AxiosError) => {
				if(!isCanceled) {
					setError(true);
				}
				captureError({
					type: "/GET API",
					name: "News widget",
					endpoint: newsUrl,
					message: "Error with getting news from endpoint",
					status: e.response ? e.response.status : 0,
					payload: e.response
				});
			})
			.finally(() => {
				if(!isCanceled) {
					setPending(false);
				}
			});

			return () => {
				isCanceled = true;
			};
	}, [count]);


	const dataIsReady = data && Array.isArray(data) && data.length > 0;

	if(error || (!pending && !dataIsReady)) {
		return <LoadingNewsFailed url={url} />;
	}

	logger("News data", {
		dataTypeof: typeof data,
		isArray: Array.isArray(data),
		ready: dataIsReady
	});

	return (
		<div className="m-news">
			<div className="m-news__list">
				{
					pending
						? new Array(count).fill(count).map((i, index) => <NewsPlaceholder key={index} />)
						: data.slice(0, count).map(item => <NewsItem key={item.id} item={item}/>)
				}
			</div>
			<Button href={url}>{ t("More news") }</Button>
		</div>
	);
};

export default NewsWidget;
