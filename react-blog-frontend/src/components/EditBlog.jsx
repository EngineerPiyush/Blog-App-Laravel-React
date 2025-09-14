import React, { useEffect, useState } from "react";
import Editor from "react-simple-wysiwyg";
import { useForm } from "react-hook-form";
import { toast } from "react-toastify";
import { useNavigate, useParams } from "react-router-dom";
const API_URL = import.meta.env.VITE_API_URL;
function EditBlog() {
  const [blog, setBlog] = useState();
  const params = useParams();
  const {
    register,
    handleSubmit,
    watch,
    reset,
    formState: { errors },
  } = useForm();
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

  const fetchBlog = async () => {
    const res = await fetch(`${API_URL}/api/blog/` + params.id);
    const result = await res.json();
    setBlog(result.data);
    setHtml(result.data.description);
    reset(result.data);
  };

  const formSubmit = async (data) => {
    const new_data = { ...data, description: html};
    if (imageId) {
      new_data.image_id = imageId; // only send if new image uploaded
    }
    const res = await fetch(`${API_URL}/api/blog/` + params.id, {
      method: "PUT",
      headers: {
        "content-type": "application/json",
      },
      body: JSON.stringify(new_data),
    });
    if (res.status) {
      toast.success("Blog updated successfully");
      navigate("/");
    } else {
      toast.error(res.message);
    }
  };

  useEffect(() => {
    fetchBlog();
  }, []);
  return (
    <div className="container mb-5">
      <div className="d-flex justify-content-between pt-5 mb-2">
        <h4>Edit Blog</h4>
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
              <div className="mt-3">
                {blog && blog.image && (
                  <img
                    className="w-50"
                    src={`${API_URL}/uploads/blogs/${blog.image}`}
                    alt="Blog Image"
                  />
                )}
              </div>
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
            <button className="btn btn-dark">Update</button>
          </div>
        </form>
      </div>
    </div>
  );
}

export default EditBlog;
