import React, { useEffect, useState } from "react";
import clsx from "clsx";

export default function ({ text, loadingText = null, size, variant, isLoading }) {
  const [sizeClass, setSizeClass] = useState("");
  const [variantClass, setVariantClass] = useState("");

  if (!loadingText) loadingText = text;

  useEffect(() => {
    switch (size) {
      case "sm":
        setSizeClass("btn-sm");
        break;

      case "lg":
        setSizeClass("btn-lg");
        break;

      default:
        break;
    }

    switch (variant) {
      case "secondary":
        setVariantClass("btn-secondary");
        break;

      case "success":
        setVariantClass("btn-success");
        break;

      case "danger":
        setVariantClass("btn-danger");
        break;

      default:
        setVariantClass("btn-primary");
        break;
    }
  }, []);

  return (
    <button type="submit" className={clsx("btn", variantClass, sizeClass)} disabled={isLoading}>
      {isLoading && <span className="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>}
      {isLoading ? loadingText : text}
    </button>
  );
}
