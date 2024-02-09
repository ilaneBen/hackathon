import React, { useRef } from "react";
import monkey from "../../Components/ThreeJS";
import "./style.scss";

export default function () {
  const ref = useRef();

  monkey(200, 200, ref, "footer", true);

  return (
    <footer className="container-fluid">
      <div className="py-3 my-4">
        <div className="d-flex justify-content-center monkey_div" ref={ref} />
        <p className="text-center p-3 border-top text-muted">© 2024 IT Akademy</p>
      </div>
    </footer>
  );
}
