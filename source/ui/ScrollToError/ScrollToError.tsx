import { useEffect} from "react";

type Props = {
  parentSelector: string,
  errorSelector: string,
  errors: any,
  offset?: number,
}

const ScrollToError = ({parentSelector, errorSelector, offset = 100, errors}: Props) => {
  useEffect(() => {
    if(window.pageYOffset < offset) {
      return;
    }
    const scope = document.querySelector(parentSelector);

    if(!scope) {
      return;
    }
    const elementWithError = scope.querySelector(errorSelector);

    if(!elementWithError) {
      return;
    }

    const elementTop = elementWithError.getBoundingClientRect().top;
    const yOffset = window.pageYOffset;
    const elementPosition = elementTop + yOffset;
    const top = elementPosition - offset > 0 ? elementPosition - offset : 0;

    window.scrollTo({
      top: top,
      left: 0,
      behavior: "smooth"
    });

  }, [errorSelector, errors, offset, parentSelector]);

  return null;
};

export default ScrollToError;
