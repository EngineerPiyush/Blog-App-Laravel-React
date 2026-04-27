// Shimmer.jsx
import "./shimmer.css";

export default function Shimmer() {
  return (
    <div className="shimmer-container">
      {[1,2,3,4].map((_, i) => (
        <div key={i} className="shimmer-card">
          <div className="shimmer-image"></div>
          <div className="shimmer-text"></div>
          <div className="shimmer-text short"></div>
        </div>
      ))}
    </div>
  );
}
