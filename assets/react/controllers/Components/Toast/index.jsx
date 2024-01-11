import React from "react";

export default function ({ type }) {
  const Icon = () => {
    <i class="bi bi-check-circle-fill"></i>;
  };

  return (
    <>
      <div className="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div className="toast-header">
          <img src="..." className="rounded me-2" alt="..." />
          <strong className="me-auto">Bootstrap</strong>
          <button type="button" className="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div className="toast-body">Hello, world! This is a toast message.</div>
      </div>
    </>
  );
}
