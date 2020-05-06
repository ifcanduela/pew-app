const m = document.querySelector("meta[name=base-url]");

export const baseURL = m ? m.content.replace(/\/+$/, "") : "/";

export default function url(...path) {
    const segments = path.map(p => p.replace(/(^\/+|\/+$)/, ""));
    return baseURL + "/" + segments.join("/");
}
