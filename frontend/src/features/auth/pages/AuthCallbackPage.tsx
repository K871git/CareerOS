import { useEffect } from "react";
import { useNavigate } from "react-router-dom";
import { useAuth } from "../../../store/authStore";
import { useAuthOverlay } from "../../../contexts/AuthOverlayContext";
import api from "../../../api/axios";

export default function AuthCallbackPage() {
  const navigate = useNavigate();
  const { login } = useAuth();
  const { showWelcome } = useAuthOverlay();

  useEffect(() => {
    const params = new URLSearchParams(window.location.search);
    const code = params.get("code");

    if (!code) {
      navigate("/?modal=login");
      return;
    }

    // Exchange the one-time code for the real token server-side.
    // The token never appears in the URL or browser history.
    api
      .post<{ data: { user: { name: string }; token: string } }>(
        "/v1/auth/oauth/exchange",
        { code }
      )
      .then((res) => {
        const { user, token } = res.data.data;
        showWelcome(user.name, () => {
          login(user as any, token);
          navigate("/dashboard");
        });
      })
      .catch(() => {
        navigate("/?modal=login");
      });
  }, []);

  return (
    <div
      style={{
        display: "flex",
        alignItems: "center",
        justifyContent: "center",
        height: "100vh",
        color: "var(--color-text-muted)",
        fontSize: "0.9rem",
      }}
    >
      Signing you in…
    </div>
  );
}
