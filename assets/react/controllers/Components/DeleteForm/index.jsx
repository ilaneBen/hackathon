import React, { useState } from "react";
import Button from "../../Components/Button";
import toast from "react-hot-toast";

export default function ({ closeRef, elementToDelete, csrf, setState, deleteUrl, buttonText = "Supprimer" }) {
  const [isLoading, setIsLoading] = useState(false);

  const submitForm = (e) => {
    e.preventDefault();
    setIsLoading(true);

    const formData = new FormData(e.target);

    fetch(deleteUrl, {
      method: "POST",
      body: formData,
    })
      .then((res) => res.json())
      .then((res) => {
        if (res?.code === 200) {
          toast.success(res?.message);
          closeRef.current.click();

          // Remove the deleted element from the state
          setState((prevState) => prevState.filter((el) => elementToDelete.id !== el.id));
        } else {
          toast.error(res?.message);
        }
      })
      .finally(() => setIsLoading(false));
  };

  return (
    <form onSubmit={submitForm}>
      <p>Attention, vous êtes sur le point de supprimer un élément.</p>

      <input type="hidden" name="deleteCsrf" value={csrf} />

      <div className="text-center mt-3">
        <Button text={buttonText} loadingText="Suppression..." isLoading={isLoading} variant="danger" />
      </div>
    </form>
  );
}
