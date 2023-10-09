import AdminLayout from "@/layouts/AdminLayout";
import UserPicture from "./components/UserPicture";
import SettingsNavigation from "./components/SettingsNavigation";
import PersonalInformation from "./components/PersonalInformation";

function SettingsPage() {
    return (
        <AdminLayout>
            <div className="h-full rounded-lg border shadow-lg grid grid-cols-[300px_auto] overflow-hidden">
                <div className="space-y-4 p-6 overflow-auto">
                    <UserPicture />
                    <SettingsNavigation />
                </div>
                <div className="overflow-auto p-6">
                    <PersonalInformation />
                </div>
            </div>
        </AdminLayout>
    );
}

export default SettingsPage;
