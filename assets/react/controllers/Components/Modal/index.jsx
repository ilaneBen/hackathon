import React, { Children } from "react";

export default function ({ title, children, closeRef }) {
  const element = Children.only(children);

  return (
    <div className="modal fade" id="projectModal" tabIndex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div className="modal-dialog">
        <div className="modal-content">
          <div className="modal-header">
            <h1 className="modal-title fs-5" id="exampleModalLabel">
              {title}
            </h1>
            <button
              type="button"
              className="btn-close"
              data-bs-dismiss="modal"
              aria-label="Close"
              ref={closeRef}
            ></button>
          </div>
          <div className="modal-body">{element}</div>
        </div>
      </div>
    </div>
  );
}
