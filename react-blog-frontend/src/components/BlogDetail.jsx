import React, { useEffect, useState } from "react";
import { useParams } from "react-router-dom";
const API_URL = import.meta.env.VITE_API_URL;
const BlogDetail = () => {
  const [blog, setBlog] = useState();
  const params = useParams();

  const fetchBlog = async () => {
    const res = await fetch(`${API_URL}/api/blog/` + params.id);
    const result = await res.json();
    setBlog(result.data);
  }
  useEffect(() => {
    fetchBlog();
  }, []);

    if (!blog) {
    return <p>Loading...</p>;
  }

  return (
    <div className="container">
      <div className="d-flex justify-content-between pt-5 mb-2">
        <h2>{blog.title}</h2>
        <div>
          <a href="/" className="btn btn-dark">
            Back to Blogs
          </a>
        </div>
      </div>
      <div className="col-md-12">
        <p>by <strong>{blog.author}</strong> on {blog.date}</p>
        {
          (blog.image) && <img  className="w-50" src={`${API_URL}/uploads/blogs/${blog.image}`} alt="Blog Image" />
        }
        <div className="mt-5" dangerouslySetInnerHTML={{__html: blog.description}}></div>
      </div>
    </div>
  );
};

export default BlogDetail;
