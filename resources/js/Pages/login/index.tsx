import { Button } from "@/Components/ui/button";
import { Link } from "@inertiajs/react";

function LoginPage() {
    return (
        <div className="p-4">
            <Link href="/dashboard"> goto dashboard</Link>
            <Button>Sample content</Button>
        </div>
    );
}

export default LoginPage;
