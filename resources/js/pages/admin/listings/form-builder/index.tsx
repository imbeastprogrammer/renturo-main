import AdminLayout from '@/layouts/AdminLayout';

function FormBuilder() {
    return <div>FormBuilder</div>;
}

FormBuilder.layout = (page: any) => <AdminLayout>{page}</AdminLayout>;

export default FormBuilder;
