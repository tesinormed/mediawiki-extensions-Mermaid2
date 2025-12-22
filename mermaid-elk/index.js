import mermaid from "mermaid";
import elkLayouts from "@mermaid-js/layout-elk";

window.elkLayouts = elkLayouts;
window.mermaid = mermaid;

mermaid.registerLayoutLoaders(elkLayouts);
