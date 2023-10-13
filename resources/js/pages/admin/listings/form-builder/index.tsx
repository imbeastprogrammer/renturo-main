import AdminLayout from '@/layouts/AdminLayout';
import Toolbox from './components/Toolbox';

function FormBuilder() {
    return (
        <div>
            <h1 className='mb-4 text-[30px] font-semibold'>Form Builders</h1>
            <div className='grid grid-cols-[300px_1fr]'>
                <Toolbox />
            </div>
        </div>
    );
}

FormBuilder.layout = (page: any) => <AdminLayout>{page}</AdminLayout>;

export default FormBuilder;
