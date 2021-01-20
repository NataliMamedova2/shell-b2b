import React, { ReactNode, HTMLProps } from "react";
import Text, { TTextColor } from "./Text";
import "./styles.scss";

type Props = {
	as?: any,
	color?: TTextColor,
	children?: ReactNode,
	to?: string,
} & HTMLProps<HTMLAnchorElement>;

export const H1 = ({children, ...props}: Props) => (
	<Text {...props} type="h1">{children}</Text>
);

const H2 = ({children, ...props}: Props) => (
	<Text {...props} type="h2">{children}</Text>
);

const H3 = ({children, ...props}:Props) => (
	<Text {...props} type="h3">{children}</Text>
);

const H4 = ({children, ...props}:Props) => (
	<Text {...props} type="h4">{children}</Text>
);

const H5 = ({children, ...props}:Props) => (
	<Text {...props} type="h5">{children}</Text>
);

const Paragraph = ({children, ...props}:Props) => (
	<Text {...props} type="paragraph">{children}</Text>
);

const Caption = ({children, ...props}: Props) => (
	<Text {...props} type="caption">{children}</Text>
);

const Note = ({children, ...props}: Props) => (
	<Text {...props} type="note">{children}</Text>
);

const Label = ({children, ...props}: Props) => (
	<Text {...props} type="label">{children}</Text>
);

export { H2, H3, H4, H5, Paragraph, Caption, Label, Note };

