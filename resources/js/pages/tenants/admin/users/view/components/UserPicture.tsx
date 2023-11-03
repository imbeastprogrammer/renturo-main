import profilePicture from "@/assets/profile.png";
import { Badge } from "@/components/ui/badge";
import { CheckCircleIcon } from "lucide-react";

function UserPicture() {
    return (
        <div className="w-full space-y-4 h-max flex flex-col items-center bg-off-white rounded-lg p-4">
            <img className="h-[100px] w-[100px]" src={profilePicture} />
            <div className="flex items-center gap-4 text-[18px] font-semibold">
                <h1>Jane Cooper</h1>
                <CheckCircleIcon className="text-metalic-blue" />
            </div>
            <Badge className="bg-metalic-blue pointer-events-none p-2 uppercase px-8">
                Admin
            </Badge>
        </div>
    );
}

export default UserPicture;
