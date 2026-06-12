import { __, sprintf } from '@wordpress/i18n';
import { useState } from '@wordpress/element';
import {
	useBlockProps,
	InspectorControls,
	BlockControls,
	RichText,
} from '@wordpress/block-editor';
import {
	Placeholder,
	Button,
	TextControl,
	SelectControl,
	ToggleControl,
	RangeControl,
	PanelBody,
	ToolbarGroup,
	ToolbarButton,
	Notice,
} from '@wordpress/components';

const ASPECT_RATIOS = [
	{ label: '16 : 9 ' + __( '(Widescreen)', 'mtdev-privacy-video-block' ), value: '16/9' },
	{ label: '4 : 3 ' + __( '(Standard)', 'mtdev-privacy-video-block' ), value: '4/3' },
	{ label: '1 : 1 ' + __( '(Square)', 'mtdev-privacy-video-block' ), value: '1/1' },
	{ label: '9 : 16 ' + __( '(Vertical / Shorts)', 'mtdev-privacy-video-block' ), value: '9/16' },
	{ label: '21 : 9 ' + __( '(Ultrawide)', 'mtdev-privacy-video-block' ), value: '21/9' },
];

function extractVideoId( url ) {
	if ( ! url ) {
		return '';
	}
	url = String( url ).trim();
	const pattern =
		/(?:youtube(?:-nocookie)?\.com\/(?:[^/\n\s]+\/\S+\/|(?:v|e(?:mbed)?|shorts|live)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/i;
	const match = url.match( pattern );
	if ( match ) {
		return match[ 1 ];
	}
	return /^[a-zA-Z0-9_-]{11}$/.test( url ) ? url : '';
}

const PlayIcon = () => (
	<svg viewBox="0 0 24 24" focusable="false" aria-hidden="true">
		<path d="M8 5v14l11-7z" />
	</svg>
);

export default function Edit( { attributes, setAttributes } ) {
	const [ inputUrl, setInputUrl ] = useState( attributes.url || '' );
	const [ touched, setTouched ] = useState( false );

	const blockProps = useBlockProps( {
		style: attributes.maxWidth ? { maxWidth: attributes.maxWidth + 'px' } : undefined,
	} );

	const validId = extractVideoId( inputUrl );

	const submitUrl = () => {
		setTouched( true );
		if ( validId ) {
			setAttributes( { url: inputUrl, videoId: validId } );
		}
	};

	if ( ! attributes.videoId ) {
		return (
			<div { ...blockProps }>
				<Placeholder
					icon="video-alt3"
					label={ __( 'YouTube (Privacy)', 'mtdev-privacy-video-block' ) }
					instructions={ __(
						'Paste a YouTube URL. It is embedded via youtube-nocookie.com, and nothing loads from YouTube until the visitor clicks play.',
						'mtdev-privacy-video-block'
					) }
				>
					<div className="mtdevpvb-placeholder-form">
						<TextControl
							label={ __( 'YouTube URL', 'mtdev-privacy-video-block' ) }
							hideLabelFromVision
							type="url"
							placeholder={ __(
								'https://www.youtube.com/watch?v=…',
								'mtdev-privacy-video-block'
							) }
							value={ inputUrl }
							onChange={ ( v ) => {
								setInputUrl( v );
								setTouched( true );
							} }
							onKeyDown={ ( e ) => {
								if ( e.key === 'Enter' ) {
									submitUrl();
								}
							} }
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
						<Button
							variant="primary"
							onClick={ submitUrl }
							disabled={ ! validId }
							__next40pxDefaultSize
						>
							{ __( 'Embed', 'mtdev-privacy-video-block' ) }
						</Button>
					</div>
					{ touched && inputUrl && ! validId && (
						<Notice status="error" isDismissible={ false }>
							{ __(
								'That does not look like a valid YouTube URL.',
								'mtdev-privacy-video-block'
							) }
						</Notice>
					) }
				</Placeholder>
			</div>
		);
	}

	const accessibleTitle = attributes.title
		? sprintf(
				/* translators: %s: video title. */
				__( 'YouTube video (click-to-load): %s', 'mtdev-privacy-video-block' ),
				attributes.title
		  )
		: __(
				'YouTube video (loads when the visitor clicks play)',
				'mtdev-privacy-video-block'
		  );

	return (
		<>
			<BlockControls>
				<ToolbarGroup>
					<ToolbarButton
						icon="edit"
						label={ __( 'Replace video', 'mtdev-privacy-video-block' ) }
						onClick={ () => {
							setInputUrl( attributes.url || '' );
							setAttributes( { videoId: '' } );
						} }
					/>
				</ToolbarGroup>
			</BlockControls>

			<InspectorControls>
				<PanelBody title={ __( 'Video settings', 'mtdev-privacy-video-block' ) } initialOpen>
					<TextControl
						label={ __( 'Title (for accessibility)', 'mtdev-privacy-video-block' ) }
						help={ __( 'Used as the iframe title for screen readers.', 'mtdev-privacy-video-block' ) }
						value={ attributes.title }
						onChange={ ( v ) => setAttributes( { title: v } ) }
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					<SelectControl
						label={ __( 'Aspect ratio', 'mtdev-privacy-video-block' ) }
						value={ attributes.aspectRatio }
						options={ ASPECT_RATIOS }
						onChange={ ( v ) => setAttributes( { aspectRatio: v } ) }
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					<RangeControl
						label={ __( 'Maximum width (px)', 'mtdev-privacy-video-block' ) }
						help={ __( '0 = full width of the content area.', 'mtdev-privacy-video-block' ) }
						value={ attributes.maxWidth }
						min={ 0 }
						max={ 1920 }
						step={ 10 }
						onChange={ ( v ) => setAttributes( { maxWidth: v || 0 } ) }
					/>
					<TextControl
						label={ __( 'Start time (seconds)', 'mtdev-privacy-video-block' ) }
						type="number"
						min={ 0 }
						value={ attributes.startTime }
						onChange={ ( v ) => setAttributes( { startTime: parseInt( v, 10 ) || 0 } ) }
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					<ToggleControl
						label={ __( 'Show related videos at the end', 'mtdev-privacy-video-block' ) }
						checked={ attributes.relatedVideos }
						onChange={ ( v ) => setAttributes( { relatedVideos: v } ) }
						__nextHasNoMarginBottom
					/>
				</PanelBody>
			</InspectorControls>

			<figure { ...blockProps }>
				<div
					className="mtdevpvb-frame"
					style={ { aspectRatio: attributes.aspectRatio.replace( '/', ' / ' ) } }
				>
					<span className="mtdevpvb-play" role="img" aria-label={ accessibleTitle }>
						<span className="mtdevpvb-play-icon" aria-hidden="true">
							<PlayIcon />
						</span>
						<span className="mtdevpvb-play-text">
							{ __( 'Load video', 'mtdev-privacy-video-block' ) }
						</span>
						<span className="mtdevpvb-play-note">
							{ __(
								'Click-to-load: nothing reaches YouTube until the visitor plays.',
								'mtdev-privacy-video-block'
							) }
						</span>
					</span>
				</div>
				<RichText
					tagName="figcaption"
					className="mtdevpvb-caption"
					placeholder={ __( 'Add a caption…', 'mtdev-privacy-video-block' ) }
					value={ attributes.caption }
					onChange={ ( v ) => setAttributes( { caption: v } ) }
					allowedFormats={ [ 'core/bold', 'core/italic', 'core/link' ] }
				/>
			</figure>
		</>
	);
}
