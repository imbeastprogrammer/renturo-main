import { ReactNode } from 'react';
import FormBuilderHeader from './FormBuilderHeader';

type FormBuilderLayoutProps = {
    children: ReactNode;
};

function FormBuilderLayout({ children }: FormBuilderLayoutProps) {
    return (
        <div className='grid h-screen grid-rows-[auto_1fr]'>
            <FormBuilderHeader />
            {children}
        </div>
    );
}

export default FormBuilderLayout;
