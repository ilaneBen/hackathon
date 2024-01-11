import React from "react";

export default function ({ closeRef, project }) {
  const handleDelete = () => {
    fetch(project.deleteUrl, {
      method: "POST",
    })
      .then((res) => res.json())
      .then((res) => {
        if (res?.code === 200) {
          closeRef.current.click();
        }
      });
  };

  return <button onClick={handleDelete} className="btn btn-danger"></button>;
}
