import { useEffect } from "react";

const ScrollToZero = () => {
  useEffect(() => {
    window.scrollTo(0, 0);
  }, []);
  return null;
};

export default ScrollToZero;
