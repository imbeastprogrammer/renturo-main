import AdminLayout from "@/layouts/AdminLayout";
import UserPicture from "./components/UserPicture";
import SettingsNavigation from "./components/SettingsNavigation";
import PersonalInformation from "./components/PersonalInformation";

function SettingsPage() {
    return (
        <AdminLayout>
            <div className="h-full border shadow-lg rounded-lg grid grid-cols-[300px_auto]">
                <div className="space-y-4 p-6">
                    <UserPicture />
                    <SettingsNavigation />
                </div>
                <div className="overflow-auto hide-scrollbar p-6">
                    <PersonalInformation />
                </div>
            </div>
        </AdminLayout>
    );
}

export default SettingsPage;
