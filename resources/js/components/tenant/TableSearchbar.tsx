import { FaSearch } from 'react-icons/fa';
import { Input, InputProps } from '../ui/input';

function TableSearchbar(props: InputProps) {
    return (
        <div className='relative flex w-full items-center'>
            <FaSearch className='absolute left-2 text-black/20' />
            <Input
                className='pl-10 placeholder:text-black/20 focus-visible:ring-transparent'
                {...props}
            />
        </div>
    );
}

export default TableSearchbar;
