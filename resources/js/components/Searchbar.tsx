import { Input } from "./ui/input";
import { SearchIcon } from "lucide-react";

function Searchbar() {
    return (
        <div className="flex items-center relative">
            <SearchIcon className="absolute left-4" />
            <Input placeholder="Search..." className="p-6 rounded-full pl-12" />
        </div>
    );
}

export default Searchbar;
