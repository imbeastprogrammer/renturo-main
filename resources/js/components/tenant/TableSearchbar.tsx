import { SearchIcon } from 'lucide-react';
import { Input, InputProps } from '../ui/input';

function TableSearchbar(props: InputProps) {
    return (
        <div className='relative flex w-full items-center'>
            <SearchIcon className='absolute left-2 text-black/20' />
            <Input
                className='pl-10 placeholder:text-black/20 focus-visible:ring-transparent'
                {...props}
            />
        </div>
    );
}

export default TableSearchbar;
