import React, { useRef } from "react";
import monkey from "../../Components/ThreeJS";

export default function () {
  const ref = useRef();

  monkey(200, 200, ref, "footer");

  return (
    <footer className="container-fluid mt-auto">
      <div className="py-3 my-4">
        <div className="d-flex justify-content-center" ref={ref} />
        <p className="text-center p-3 border-top text-muted">© 2024 IT Akademy</p>
      </div>
    </footer>
  );
}
