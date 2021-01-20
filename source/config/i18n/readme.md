# react i18n

## Init object i18n

## Bind to App with I18nextProvider

## Usage inside component
Two key steps:
- define way for set translation 
    - HOC(class component) 
    - or HOOK (functional component)
- use passed method `t` (short from translate) with key as argument. 
For example: `<h1>{ t("Page not found") }</h1>`. Where: 
    - `t` - method from props or hook
    - `"Page not found"` - key of translation in source object/json
    
### with React Hook - `useTranslation`

```typescript jsx
import React from "react";
import {useTranslation} from "react-i18next";

const NotFoundError = () => {
	const { t } = useTranslation();
	return <h1>{t("Page not found")}</h1>
};
export default NotFoundError;
```
    
### with React HOC - `withTranslation`  

```typescript jsx
import React from "react";
import { WithTranslation, withTranslation} from "react-i18next";

const NotFoundError = ({t, ...props}: { } & WithTranslation) => {
	return <h1>{t("Page not found")}</h1>
};
export default WithTranslation()(NotFoundError);
// export default WithTranslation()(withRouter(NotFoundError));
```
