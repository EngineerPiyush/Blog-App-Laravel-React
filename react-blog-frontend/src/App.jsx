import "bootstrap/dist/css/bootstrap.min.css";
import { Route, Routes } from "react-router-dom";
import Blogs from "./components/Blogs";
import CreateBlog from "./components/CreateBlog";
import { ToastContainer } from 'react-toastify';
import BlogDetail from "./components/BlogDetail";
import EditBlog from "./components/EditBlog";

function App() {

  return (
    <>
      <div className="bg-dark text-center py-2 shadow-lg">
        <h1 className="text-white">Blog Application</h1>
      </div>
      <Routes>
        <Route path="/" element={<Blogs />} />
        <Route path="/create" element={<CreateBlog />} />
        <Route path="/blog/:id" element={<BlogDetail/>}></Route>
        <Route path="/blog/edit/:id" element={<EditBlog/>}></Route>
      </Routes>
      <ToastContainer />
    </>
  );
}

export default App;
