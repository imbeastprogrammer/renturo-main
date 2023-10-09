import { BellIcon, ChevronDownIcon } from "lucide-react";
import { Separator } from "./ui/separator";
import proflle from "@/assets/profile.png";

function UserButton() {
    return (
        <div className="flex h-[55px] gap-4 bg-white rounded-xl border p-2 items-center px-3">
            <div className="relative text-metalic-blue">
                <BellIcon />
                <div className="absolute -top-1 -right-1 w-2 h-2 bg-red-500 rounded-full" />
            </div>
            <Separator orientation="vertical" />
            <h1 className="text-lg font-semibold">Jane Cooper</h1>
            <div className="h-[40px] w-[40px] rounded-full p-[2px] bg-metalic-blue overflow-hidden">
                <img src={proflle} className="h-full w-full" />
            </div>
            <ChevronDownIcon />
        </div>
    );
}

export default UserButton;
