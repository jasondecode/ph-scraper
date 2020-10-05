<?php

namespace App\Services\ProductHunt\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class EntityProduct extends Model
{    
    /** @var array */
    protected $fillable = ['votes', 'name', 'featured_at', 'updated_at', 'topics', 'shortened_url', 'slug'];

    /** @var string */
    protected $homepageRequestBody = '{"operationName":"HomePage","variables":{"cursor":"","featured":true,"includePromotedPost":false,"visibleOnHomepage":true,"includeLayout":false},"query":"query HomePage($cursor: String, $postCursor: String, $featured: Boolean!, $includePromotedPost: Boolean!, $visibleOnHomepage: Boolean!) {  sections(first: 1, after: $cursor, featured: $featured) {    edges {      cursor      node {        id        date        cutoff_index        posts_count        cards(first: 1, after: $cursor) {          edges {            node {              ...FeedCards              __typename            }            __typename          }          __typename        }        posts(after: $postCursor, visible_on_homepage: $visibleOnHomepage) {          edges {            node {              ...PostItemList              featured_comments {                test                variant                comments {                  id                  body: body_text                  user {                    id                    ...UserImageLink                    __typename                  }                  __typename                }                __typename              }              __typename            }            __typename          }          pageInfo {            endCursor            hasNextPage            __typename          }          __typename        }        __typename      }      __typename    }    pageInfo {      endCursor      hasNextPage      __typename    }    __typename  }  ad(kind: \"feed\") @include(if: $includePromotedPost) {    ...AdFragment    __typename  }  promoted_email_campaign(promoted_type: HOMEPAGE) @include(if: $includePromotedPost) {    id    abTestName    abVariant {      id      ...PromotedEmailAbTestVariantFragment      __typename    }    ...PromotedEmailCampaignFragment    __typename  }  daily_newsletter {    id    subject    __typename  }  viewer {    id    email    has_newsletter_subscription    __typename  }  ph_homepage_og_image_url}fragment FeedCards on Card {  ...NewPostsCard  ...BestProductsFromLastWeekCard  ...MakersDiscussionCardFragment  ...GoldenKittyCardFragment  __typename}fragment NewPostsCard on NewPostsCard {  is_dismissed  kind  posts {    ...PostItemList    __typename  }  __typename}fragment PostItemList on Post {  id  ...PostItem  __typename}fragment PostItem on Post {  id  _id  comments_count  name  shortened_url  slug  tagline  updated_at  topics {    edges {      node {        id        name        slug        __typename      }      __typename    }    __typename  }  ...PostThumbnail  ...PostVoteButton  __typename}fragment PostThumbnail on Post {  id  name  thumbnail {    id    media_type    ...MediaThumbnail    __typename  }  ...PostStatusIcons  __typename}fragment MediaThumbnail on Media {  id  image_uuid  __typename}fragment PostStatusIcons on Post {  name  product_state  __typename}fragment PostVoteButton on Post {  _id  id  featured_at  updated_at  disabled_when_scheduled  has_voted  ... on Votable {    id    votes_count    __typename  }  __typename}fragment BestProductsFromLastWeekCard on BestProductsFromLastWeekCard {  posts {    ...PostItemList    __typename  }  __typename}fragment MakersDiscussionCardFragment on MakersDiscussionCard {  isDismissed  discussion {    _id    id    ...DiscussionThreadListItem    __typename  }  __typename}fragment DiscussionThreadListItem on DiscussionThread {  _id  id  title  description  descriptionHtml  slug  commentsCount  can_comment: canComment  discussionPath  canEdit  votesCount  hasVoted  createdAt  poll {    ...PollFragment    __typename  }  user {    id    name    username    headline    avatar    __typename  }  __typename}fragment PollFragment on Poll {  id  answersCount  hasAnswered  options {    id    text    imageUuid    answersCount    answersPercent    hasAnswered    __typename  }  __typename}fragment GoldenKittyCardFragment on GoldenKittyCard {  is_dismissed  category_for_voting {    id    slug    __typename  }  __typename}fragment PromotedEmailCampaignFragment on PromotedEmailCampaign {  id  _id  title  tagline  thumbnail  ctaText  __typename}fragment PromotedEmailAbTestVariantFragment on PromotedEmailAbTestVariant {  id  _id  title  tagline  thumbnail  ctaText  __typename}fragment AdFragment on LegacyAdsUnion {  ... on PromotedPost {    id    ...LegacyPromotedPostItem    __typename  }  ... on AdChannel {    id    post {      id      slug      name      updated_at      comments_count      ...PostVoteButton      __typename    }    ctaText    dealText    adName: name    adTagline: tagline    adThumbnailUuid: thumbnailUuid    adUrl: url    __typename  }  __typename}fragment LegacyPromotedPostItem on PromotedPost {  id  deal  post {    id    ...PostItem    __typename  }  name  tagline  ctaText  url  thumbnailUuid  ...ViewableImpressionSubject  __typename}fragment ViewableImpressionSubject on Node {  id  __typename}fragment UserImageLink on User {  id  _id  name  username  avatar  ...UserImage  __typename}fragment UserImage on User {  id  post_upvote_streak  name  avatar  __typename}"}';

    public function getHomepageRequestBody(): string
    {
        return $this->homepageRequestBody;
    }

    public function entity(): MorphOne
    {
        return $this->morphOne(\App\Services\Scraper\Models\Entity::class, 'entityable');
    }

    public function getTopics(): array 
    {
        return json_decode($this->topics, true);
    }
}
