import { type ClassValue, clsx } from "clsx";
import { extendTailwindMerge } from "tailwind-merge";
import config from "../../../tailwind.config";

const customMerge = extendTailwindMerge({
    classGroups: {
        "font-size": Object.keys(config.theme?.extend?.fontSize || {}).map(
            (key) => `text-${key}`
        ),
    },
});

export function cn(...inputs: ClassValue[]) {
    return customMerge(clsx(inputs));
}
