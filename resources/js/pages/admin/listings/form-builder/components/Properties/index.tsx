import { FormFields } from '../..';

type PropertiesProps = {
    items: FormFields[];
};
function Properties({ items }: PropertiesProps) {
    return (
        <div className='p-4'>
            {items.map((item) => (
                <div>{item.label}</div>
            ))}
        </div>
    );
}

export default Properties;
