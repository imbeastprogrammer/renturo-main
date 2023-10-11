import UserButton from '@/components/UserButton';

function AdminLayoutHeader() {
    return (
        <header>
            <div className='flex items-center justify-end gap-4'>
                <UserButton />
            </div>
        </header>
    );
}

export default AdminLayoutHeader;
