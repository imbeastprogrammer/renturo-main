import AdminLayout from '@/layouts/AdminLayout';

function Properties() {
    return <div>index</div>;
}

Properties.layout = (page: any) => <AdminLayout>{page}</AdminLayout>;

export default Properties;
