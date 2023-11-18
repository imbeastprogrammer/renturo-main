import { PropsWithChildren } from 'react';

function OwnerLayout(props: PropsWithChildren) {
    return <div>this is the owner layout {props.children}</div>;
}

export default OwnerLayout;
