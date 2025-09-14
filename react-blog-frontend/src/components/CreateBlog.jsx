import React from "react";
import Editor from "react-simple-wysiwyg";
import { useState } from "react";
import { useForm } from "react-hook-form";
import { toast } from "react-toastify";
import { useNavigate } from "react-router-dom";
const API_URL = import.meta.env.VITE_API_URL;
const CreateBlog = () => {
  const [html, setHtml] = useState("");
  const [imageId, setImageId] = useState("");
  const navigate = useNavigate();
  function onChange(e) {
    setHtml(e.target.value);
  }
  const handleFileChange = async (e) => {
    const file = e.target.files[0];
    const formData = new FormData();
    formData.append("image", file);

    const res = await fetch(`${API_URL}/api/save-image-temp`, {
      method: "POST",
      body: formData,
    });
    const result = await res.json();
    if (result.status == false) {
      alert(result.errors.image);
      e.target.value = null;
    }

    setImageId(result.image.id);
  };
  const {
    register,
    handleSubmit,
    watch,
    formState: { errors },
  } = useForm();

  const formSubmit = async (data) => {
    const new_data = { ...data, description: html, image_id: imageId };
    const res = await fetch(`${API_URL}/api/blogs`, {
      method: "POST",
      headers: {
        "content-type": "application/json",
      },
      body: JSON.stringify(new_data),
    });
    if (res.status) {
      toast.success("Blog created successfully");
      navigate("/");
    } else {
      toast.error(res.message);
    }
  }
  return (
    <div className="container mb-5">
      <div className="d-flex justify-content-between pt-5 mb-2">
        <h4>Create Blog</h4>
        <a href="/" className="btn btn-dark">
          Back
        </a>
      </div>
      <div className="card border-0 shadow-lg">
        <form onSubmit={handleSubmit(formSubmit)}>
          <div className="card-body">
            <div className="mb-3">
              <label className="form-label">Title</label>
              <input
                type="text"
                {...register("title", { required: true })}
                className={`form-control ${errors.title && "is-invalid"}`}
                placeholder="Title"
              />
              {errors.title && (
                <p className="invalid-feedback">Title field is required</p>
              )}
            </div>
            <div className="mb-3">
              <label className="form-label">Short Description</label>
              <textarea
                cols="30"
                rows="5"
                {...register("shortDesc")}
                className="form-control"
              ></textarea>
            </div>
            <div className="mb-3">
              <label className="form-label">Description</label>
              <Editor
                value={html}
                onChange={onChange}
                containerProps={{ style: { height: "300px" } }}
              />
            </div>
            <div className="mb-3">
              <label className="form-label">Image</label>
              <input onChange={handleFileChange} type="file" />
            </div>
            <div className="mb-3">
              <label className="form-label">Author</label>
              <input
                type="text"
                {...register("author", { required: true })}
                placeholder="Author"
                className={`form-control ${errors.author && "is-invalid"}`}
              />
              {errors.author && (
                <p className="invalid-feedback">Author field is required</p>
              )}
            </div>
            <button className="btn btn-dark">Create</button>
          </div>
        </form>
      </div>
    </div>
  );
};

export default CreateBlog;
