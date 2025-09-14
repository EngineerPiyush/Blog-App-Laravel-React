import React, { useState, useEffect } from "react";
import BlogCard from "./BlogCard";
const API_URL = import.meta.env.VITE_API_URL;
function Blogs() {
  const [blogs, setBlogs] = useState();
  const [keyword,setKeyword] = useState('');
  const fetchBlogs = async () => {
    const res = await fetch(`${API_URL}/api/blogs`);
    const result = await res.json();
    setBlogs(result.data);
  };
  const searchBlogs = async (e) => {
    e.preventDefault();
    const res = await fetch(`${API_URL}/api/blogs?keyword=`+keyword);
    const result = await res.json();
    setBlogs(result.data);
  };
  const resetSearch = () => {
    fetchBlogs();
    setKeyword('');
  }
  useEffect(() => {
    fetchBlogs();
  }, []);
  return (
    <div className="container">
      <div className="d-flex justify-content-center pt-5 mb-2">
        <form
          onSubmit={(e) => {
            searchBlogs(e);
          }}
        >
          <div className="d-flex">
            <input
              type="text"
              className="form-control"
              placeholder="Search Blogs" value={keyword} onChange={(e)=>{setKeyword(e.target.value)}}
            />
            <button className="btn btn-dark ms-3">Search</button>
            <button type="button" onClick={()=>{resetSearch()}} className="btn btn-success ms-3">Reset</button>
          </div>
        </form>
      </div>
      <div className="d-flex justify-content-between pt-5 mb-2">
        <h4>Blog</h4>
        <a href="/create" className="btn btn-dark">
          Create
        </a>
      </div>
      <div className="row">
        {blogs &&
          blogs.map((blog) => {
            return (
              <BlogCard
                blogs={blogs}
                setBlogs={setBlogs}
                blog={blog}
                key={blog.id}
              />
            );
          })}
      </div>
    </div>
  );
}

export default Blogs;
